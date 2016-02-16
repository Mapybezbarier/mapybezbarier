<?php

namespace MP\Module\Admin\Service;

use MP\Manager\ILangAwareManager;
use MP\Manager\ImageManager;
use MP\Manager\IManager;
use MP\Manager\ManagerFactory;
use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\ObjectDraftManager;
use MP\Object\ObjectHelper;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use MP\Util\Lang\Lang;
use MP\Util\Strings;
use Nette\Security\User;
use Nette\Utils\Json;
use Nette\Utils\Validators;

/**
 * Sluzba pro spravu draftu mapovych objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectDraftService
{
    /** @var ObjectDraftManager */
    protected $draftManager;

    /** @var ImageManager */
    protected $imageManager;

    /** @var ManagerFactory */
    protected $managerFactory;

    /** @var User */
    protected $user;

    /** @var Lang */
    protected $lang;

    /**
     * @param ObjectDraftManager $draftManager
     * @param ImageManager $imageManager
     * @param ManagerFactory $managerFactory
     * @param User $user
     * @param Lang $lang
     */
    public function __construct(ObjectDraftManager $draftManager, ImageManager $imageManager, ManagerFactory $managerFactory, User $user, Lang $lang)
    {
        $this->draftManager = $draftManager;
        $this->imageManager = $imageManager;
        $this->managerFactory = $managerFactory;
        $this->user = $user;
        $this->lang = $lang;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public function createByObject(array $object)
    {
        $draft = [
            'map_object_object_id' => $object['object_id'],
            'user_id' => $this->user->getId(),
            'data' => $this->initData($object),
        ];

        return $this->createDraft($object['object_id'], $draft);
    }

    /**
     * @param string $title
     *
     * @return array
     */
    public function createByTitle($title)
    {
        $object = [
            'title' => $title
        ];

        $draft = [
            'pair_key' => $title,
            'user_id' => $this->user->getId(),
            'data' => $this->initData($object),
        ];

        return $this->createDraft($title, $draft);
    }

    /**
     * @param int|string $key
     * @param array $values
     *
     * @return array|null
     */
    protected function createDraft($key, array $values)
    {
        $draft = $this->getDraftByKey($key);

        if (null === $draft) {
            $draft = $this->draftManager->persist($values);
        }

        return $draft;
    }

    /**
     * @param int $id
     * @param bool $raw
     *
     * @return array|null
     */
    public function getDraft($id, $raw = false)
    {
        $restrictor = [
            ["[" . IMapper::ID . "] = %i", $id],
            ["[user_id] = %i", $this->user->getId()],
        ];

        $draft = $this->getDraftBy($restrictor, $raw);

        return $draft;
    }

    /**
     * @param int|string $key
     * @param bool $raw
     *
     * @return array|null
     */
    public function getDraftByKey($key, $raw = false)
    {
        $restrictor = [
            ["[user_id] = %i", $this->user->getId()],
        ];

        if (Validators::isNumericInt($key)) {
            $restrictor[] = ["[map_object_object_id] = %i", $key];
        } else {
            $restrictor[] = ["[pair_key] = %s", $key];
        }

        $draft = $this->getDraftBy($restrictor, $raw);

        return $draft;
    }

    /**
     * @param array $restrictor
     * @param bool $raw
     *
     * @return array|null
     */
    protected function getDraftBy(array $restrictor, $raw = false)
    {
        $draft = $this->draftManager->findOneBy($restrictor);

        if (false === $raw) {
            $this->prepareDraft($draft);
        }

        return $draft;
    }

    /**
     * @param User|null $user
     *
     * @return array
     */
    public function getDraftsByUser(User $user = null)
    {
        $restrictor = [
            ["[user_id] = %i", $user ? $user->getId() : $this->user->getId()],
        ];

        $drafts = $this->draftManager->findAll($restrictor, ['title' => IMapper::ORDER_ASC]);

        return $drafts;
    }

    /**
     * @param int $id
     * @param array $values
     * @param array $attachements
     *
     * @return array
     */
    public function saveDraft($id, array $values, array $attachements)
    {
        $draft = $this->getDraft($id, true);

        if (null !== $draft) {
            $draft['data'] = $this->saveData($draft, $values, $attachements);

            $image = Arrays::get($values, 'image', null);
            unset($draft['image']);

            if (null !== $image) {
                $this->imageManager->persist($image, $id, ImageManager::NAMESPACE_DRAFT);
            } else {
                $this->imageManager->remove($id, ImageManager::NAMESPACE_DRAFT);

                if ($draft['map_object_object_id']) {
                    $this->imageManager->remove($draft['map_object_object_id'], ImageManager::NAMESPACE_OBJECT);
                }
            }

            unset($draft['title']);

            $draft = $this->draftManager->persist($draft);
        } else {
            throw new \Nette\InvalidArgumentException("Draft with ID '{$id}' not found");
        }

        return $draft;
    }

    /**
     * @param int $id
     */
    public function removeDraft($id)
    {
        $draft = $this->getDraft($id);

        if (null !== $draft) {
            $this->draftManager->remove($draft[IMapper::ID]);
        }

        $this->imageManager->remove($id, ImageManager::NAMESPACE_DRAFT);
    }

    /**
     * @param array $draft
     * @param array $object
     */
    public function publishDraft(array $draft, array $object)
    {
        $langs = $this->lang->getAllowed(true);

        foreach ($langs as $lang) {
            $data = $this->decodeData($draft['data'], $lang);

            $attachements = ObjectHelper::getAttachements($data);

            $this->saveObjectLangData($object[IMapper::ID], $lang, $data, $this->managerFactory->create(ObjectMetadata::TABLE));

            foreach ($attachements as $attachement => $indexes) {
                foreach ($indexes as $index => $values) {
                    $this->saveObjectLangData($object[$attachement][$index][IMapper::ID], $lang, $values, $this->managerFactory->create($attachement));
                }
            }
        }

        $this->draftManager->remove($draft[IMapper::ID]);

        $this->imageManager->publish($draft[IMapper::ID], $object['object_id']);
        $this->imageManager->remove($draft[IMapper::ID], ImageManager::NAMESPACE_DRAFT);
    }

    /**
     * @param int $id
     * @param string $lang
     * @param array $data
     *
     * @param IManager|ILangAwareManager $manager
     */
    private function saveObjectLangData($id, $lang, array $data, IManager $manager)
    {
        Validators::assert($manager, ILangAwareManager::class);

        if ($data = array_filter($data)) {
            $data = $this->prepareLangDataKeys($data);
            $manager->persistLangData($id, $lang, $data);
        }
    }

    /**
     * Pripravi data objektu pro zapersistovani. Prevadi klice z camel case notace do potrzitkove.
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareLangDataKeys(array $data)
    {
        $data = array_combine(
            array_map(Strings::class . '::toUnderscore', array_keys($data)),
            array_values($data)
        );

        return $data;
    }

    /**
     * @param array|null $draft
     */
    private function prepareDraft(&$draft)
    {
        if ($draft) {
            $draft['data'] = $this->decodeData($draft['data']);
            $draft['image'] = $this->imageManager->find($draft[IMapper::ID], ImageManager::NAMESPACE_DRAFT);
        }
    }

    /**
     * @param array $object
     *
     * @return string
     */
    private function initData(array $object)
    {
        $data = [];

        $attachements = ObjectHelper::getAttachements($object);

        foreach ($object as $key => $value) {
            $fixedKey = Strings::toCamelCase($key);

            if (in_array($key, ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::OBJECT], true)) {
                if (isset($object[IMapper::ID])) {
                    $manager = $this->managerFactory->create('map_object_lang');

                    foreach ($manager->findAll([["[map_object_id] = %i", $object[IMapper::ID]]], false) as $item) {
                        $data[$fixedKey][$item['lang_id']] = $item[$key];
                    }
                } else {
                    $data[$fixedKey][$this->lang->getLang()] = $value;
                }
            } else {
                $data[$fixedKey] = $value;
            }
        }

        foreach ($attachements as $attachement => $indexes) {
            foreach ($indexes as $index => $values) {
                foreach ($values as $key => $value) {
                    $fixedKey = Strings::toCamelCase($key);

                    if (in_array($key, ObjectMetadata::$LANG_AWARE_COLUMNS[$attachement], true)) {
                        if (isset($object[IMapper::ID])) {
                            $manager = $this->managerFactory->create("{$attachement}_lang");

                            foreach ($manager->findAll([["[{$attachement}_" . IMapper::ID . "] = %i", $values[IMapper::ID]]], false) as $item) {
                                $data[$attachement][$index][$fixedKey][$item['lang_id']] = $item[$key];
                            }
                        } else {
                            $data[$attachement][$index][$fixedKey][$this->lang->getLang()] = $value;
                        }
                    } else {
                        $data[$attachement][$index][$fixedKey] = $value;
                    }
                }
            }
        }

        return Json::encode($data);
    }

    /**
     * @param array $draft
     * @param array $object
     * @param array $attachements
     *
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    private function saveData(array $draft, array $object, array $attachements)
    {
        $data = Json::decode($draft['data'], Json::FORCE_ARRAY);

        $attachementsData = ObjectHelper::getAttachements($object);

        foreach ($object as $key => $value) {
            $fixedKey = Strings::toUnderscore($key);

            if (in_array($fixedKey, ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::OBJECT], true)) {
                $data[$key][$this->lang->getLang()] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        foreach ($attachements as $attachement => $indexes) {
            $attachementIndexes = [];

            foreach ($indexes as $index) {
                $attachementValues = Arrays::get($data, [$attachement, $index], []);

                if ($values = Arrays::get($attachementsData, [$attachement, $index], [])) {
                    foreach ($values as $key => $value) {
                        $fixedKey = Strings::toUnderscore($key);

                        if (in_array($fixedKey, ObjectMetadata::$LANG_AWARE_COLUMNS[$attachement], true)) {
                            $attachementValues[$key][$this->lang->getLang()] = $value;
                        } else {
                            $attachementValues[$key] = $value;
                        }
                    }
                }

                $attachementIndexes[] = $attachementValues;
            }

            $data[$attachement] = $attachementIndexes;
        }

        return Json::encode($data);
    }

    /**
     * @param string $data
     * @param string|null $lang
     *
     * @return array
     * @throws \Nette\Utils\JsonException
     */
    private function decodeData($data, $lang = null)
    {
        $object = Json::decode($data, Json::FORCE_ARRAY);

        $data = [];

        $attachements = ObjectHelper::getAttachements($object);

        foreach ($object as $key => $value) {
            $fixedKey = Strings::toUnderscore($key);

            if (in_array($fixedKey, ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::OBJECT], true)) {
                $data[$key] = Arrays::get($value, $lang ?: $this->lang->getLang(), null);
            } else if (null === $lang) {
                $data[$key] = $value;
            }
        }

        foreach ($attachements as $attachement => $indexes) {
            foreach ($indexes as $index => $values) {
                foreach ($values as $key => $value) {
                    $fixedKey = Strings::toUnderscore($key);

                    if (in_array($fixedKey, ObjectMetadata::$LANG_AWARE_COLUMNS[$attachement], true)) {
                        $data[$attachement][$index][$key] = Arrays::get($value, $lang ?: $this->lang->getLang(), null);
                    } else if (null === $lang) {
                        $data[$attachement][$index][$key] = $value;
                    }
                }
            }
        }

        return $data;
    }
}
