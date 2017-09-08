<?php

namespace MP\Module\SourceDetail;

use Dibi\DateTime;
use Kdyby\Translation\Translator;
use MP\Module\Web\Service\ObjectService;
use MP\Object\ObjectHelper;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Http\Url;
use Nette\InvalidArgumentException;

/**
 * Sluzba pro pripravu dat o mapovych objektech pro detail
 */
class DetailService
{
    /** @var ObjectService */
    protected $objectService;

    /** @var SourceDetailFactory */
    protected $sourceDetailFactory;

    /** @var Translator */
    protected $translator;

    /** @var array */
    protected $categories;

    /**
     * @param ObjectService $objectService
     * @param SourceDetailFactory $sourceDetailFactory
     * @param Translator $translator
     * @param array $categories
     */
    public function __construct(ObjectService $objectService, SourceDetailFactory $sourceDetailFactory, Translator $translator, array $categories)
    {
        $this->objectService = $objectService;
        $this->sourceDetailFactory = $sourceDetailFactory;
        $this->translator = $translator;
        $this->categories = Arrays::flip($categories);
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public function getDetail(array $object)
    {
        return $this->prepareDetailData($object);
    }

    /**
     * Vrati data pro vypis obou urovni detailu objektu
     *
     * @param int $id
     *
     * @return array
     */
    public function getDetailById($id)
    {
        $object = $this->objectService->getObjectByObjectId($id);

        return $this->prepareDetailData($object);
    }

    /**
     * Sestavi informace pro vykresleni detailu (pro obe urovne)
     *
     * @param array $object
     *
     * @return array
     */
    protected function prepareDetailData($object)
    {
        $outdatedLimit = $object['mapping_date']->modifyClone('+ 3 years');

        try {
            $url = new Url($object['web_url']);

            if (!$url->getScheme()) {
                $url->setScheme('http');
            }

            $stringUrl = trim($url->getAuthority() . $url->getBasePath() . $url->getRelativeUrl(), '/');
            $linkUrl = $url->getAbsoluteUrl();
        } catch (InvalidArgumentException $e) {
            $linkUrl = '';
            $stringUrl = '';
        }

        $ret = [
            'id' => $object['object_id'],
            'title' => $object['title'],
            'category' => $this->getCategory($object),
            'outdated' => empty($object['mapping_date']) || ($outdatedLimit <= new DateTime()),
            'address' => ObjectHelper::getAddressString($object),
            'string_url' => $stringUrl,
            'link_url' => $linkUrl,
            'longitude' => $object['longitude'],
            'latitude' => $object['latitude'],
            'source' => $object['source'],
            'owner' => $object['data_owner_url'],
            'accessibility' => [
                'id' => $object['accessibility_id'],
                'title' => $object['accessibility'],
            ],
            'descriptions' => $this->getDescriptions($object),
            'source_id' => $object['source_id'],
        ];

        $sourceDetail = $this->sourceDetailFactory->create($object);
        $ret = array_merge($ret, $sourceDetail->prepareSourceData($object));

        return $ret;
    }


    /**
     * Pripravi popisne texty objektu a jeho casti.
     *
     * @param array $object
     *
     * @return array
     */
    protected function getDescriptions($object)
    {
        $desriptions = [
            'object' => $object['description'],
            'mainEntrance' => $object['entrance1_has_description'],
            'sideEntrance' => $object['entrance2_has_description'],
            'interior' => $object['object_has_description'],
            'rampskids' => [],
            'platform' => [],
            'elevator' => [],
            'wc' => [],
        ];

        $mapping = [
            ObjectMetadata::RAMP_SKIDS => 'ramp_skids_has_description',
            ObjectMetadata::PLATFORM => 'platform_has_description',
            ObjectMetadata::ELEVATOR => 'elevator_has_description',
            ObjectMetadata::WC => 'wc_has_description',
        ];

        foreach ($mapping as $key => $description) {
            foreach ($object[$key] as $values) {
                if ($description = Arrays::get($values, $description, null)) {
                    $desriptions[$key][] = $description;
                }
            }
        }

        return $desriptions;
    }

    /**
     * Pripravi kategorii objektu.
     *
     * Pokud se jedna o objekt z kategorie jine, pak nastavuje jako title custom type, pokud tento neni prazdny.
     *
     * @param array $object
     *
     * @return array
     */
    protected function getCategory($object)
    {
        if (ObjectMetadata::CATEGORY_OTHER === $object['object_type']) {
            if ($object['object_type_custom']) {
                $title = $object['object_type_custom'];
            } else {
                $title = $this->translator->translate('messages.enum.value.category.otherObjectCategory');
            }
        } else {
            $title = $this->translator->translate('messages.enum.value.category.' . Strings::firstLower($object['object_type']));
        }

        $category = [
            'id' => Arrays::get($this->categories, $object['object_type_id'], 'other'),
            'title' => $title,
        ];

        return $category;
    }
}
