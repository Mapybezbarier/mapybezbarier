<?php

namespace MP\Module\Admin\Service;

use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\LogManager;
use MP\Util\Strings;
use Nette\Security\User;
use Nette\Utils\Paginator;

/**
 * Sluzba pro logovani akci uzivatelu v RS
 * vc. CRON akci spoustenych v kontextu konkretniho uzivatele (import)
 */
class LogService
{
    const ACTION_IMPORT_MANUAL = 'manual';
    const ACTION_IMPORT_AUTO_CREATE = 'automaticCreate';
    const ACTION_IMPORT_AUTO_DELETE = 'automaticDelete';
    const ACTION_IMPORT_AUTO_RUN = 'automaticRun';
    const ACTION_IMPORT_LICENSE_CREATE = 'licenseCreate';
    const ACTION_IMPORT_LICENSE_EDIT = 'licenseEdit';
    const ACTION_IMPORT_LICENSE_DELETE = 'licenseDelete';

    const ACTION_USER_LOGIN = 'login';
    const ACTION_USER_LOGOUT = 'logout';
    const ACTION_USER_PWD_CHANGE = 'passwordChange';
    const ACTION_USER_PWD_RESET = 'passwordReset';
    const ACTION_USER_CREATE = 'create';
    const ACTION_USER_EDIT = 'edit';
    const ACTION_USER_DELETE = 'delete';

    const ACTION_OBJECT_DELETE = 'delete';
    const ACTION_OBJECT_CREATE = 'create';
    const ACTION_OBJECT_EDIT = 'edit';
    const ACTION_OBJECT_SPLIT = 'split';
    const ACTION_OBJECT_JOIN = 'join';
    const ACTION_OBJECT_REVERT = 'revert';
    const ACTION_OBJECT_OWNER = 'owner';

    /** @var LogManager */
    protected $manager;

    /** @var User */
    protected $user;
    /**
     * @var LogDetailService
     */
    protected $detailService;
    /**
     * @var LogRestrictorBuilder
     */
    protected $restrictorBuilder;

    /**
     * @param LogManager $manager
     * @param User $user
     * @param LogDetailService $detailService
     * @param LogRestrictorBuilder $restrictorBuilder
     */
    public function __construct(
        LogManager $manager,
        User $user,
        LogDetailService $detailService,
        LogRestrictorBuilder $restrictorBuilder
    ) {
        $this->manager = $manager;
        $this->user = $user;
        $this->detailService = $detailService;
        $this->restrictorBuilder = $restrictorBuilder;
    }

    /**
     * Zaloguje akci uzivatele backendu
     *
     * @param string $moduleKey
     * @param string $actionKey
     * @param int|null $id
     * @param string|null $title
     * @param string|null $customData
     * @param int|null $userId
     */
    public function log($moduleKey, $actionKey, $id = null, $title = null, $customData = null, $userId = null)
    {
        if ($userId === null) {
            $userId = $this->user->getId();
        }

        $this->manager->persist([
            'module_key' => $moduleKey,
            'action_key' => $actionKey,
            'changed_id' => $id,
            'title' => $title,
            'custom_data' => $customData,
            'user_id' => $userId,
        ]);
    }

    /**
     * Vraci pripravena data pro vypis logu
     *
     * @param array $restrictions
     * @param Paginator $paginator
     *
     * @return array
     */
    public function findListData(array $restrictions, Paginator $paginator)
    {
        $restrictor = $this->restrictorBuilder->getRestrictor($restrictions);

        $ret = $this->manager->findAll($restrictor, ['created' => IMapper::ORDER_DESC], $paginator);
        
        foreach ($ret as &$item) {
            $item['link'] = $this->getLogDetailLink($item);
        }

        unset($item);

        return $ret;
    }

    /**
     * Sestavi odkaz pro detail zaznamu logu, moznosti:
     *     nema detail
     *     detail se vypisuje v tomto modulu
     *     detailem je jiny vypis
     *
     * @param array $item
     *
     * @return string|null
     */
    protected function getLogDetailLink($item)
    {
        $ret = null;

        $callbackMethod = "getLink" . Strings::firstUpper($item['module_key']) . Strings::firstUpper($item['action_key']);
        $callback = [$this->detailService, $callbackMethod];

        if (is_callable($callback)) {
            $ret = $callback($item);
        }

        return $ret;
    }

    /**
     * Pripravi data pro vypis detailu a urci sablonu, kterou se ma vykreslit
     * @param int $id
     *
     * @return array|null
     */
    public function getDetailParams($id)
    {
        $ret = null;

        $restrictor = $this->restrictorBuilder->getRestrictor();
        $restrictor[] = ['[l].[id] = %i', $id];

        $item = $this->manager->findOneBy($restrictor);

        if ($item) {
            $callbackMethod = "getDetail" . Strings::firstUpper($item['module_key']) . Strings::firstUpper($item['action_key']);
            $callback = [$this->detailService, $callbackMethod];

            if (is_callable($callback)) {
                $ret = $callback($item);
            } else {
                $ret = $this->detailService->getStandardDetail($item);
            }
        }

        return $ret;
    }

    /**
     * @param array $restrictions
     *
     * @return int
     */
    public function getListCount(array $restrictions)
    {
        $restrictor = $this->restrictorBuilder->getRestrictor($restrictions);

        return $this->manager->findCount($restrictor);
    }
}
