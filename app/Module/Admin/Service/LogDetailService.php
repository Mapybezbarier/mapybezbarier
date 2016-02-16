<?php

namespace MP\Module\Admin\Service;

use MP\Util\Arrays;
use Nette\Application\LinkGenerator;
use Nette\Utils\Json;


/**
 * Sluzba pro detail logu akce uzivatele
 * Obsahuje 2 typy verejnych metod:
 * a] getLinkModulAkce: sestavi odkaz do detailu
 * b] getDetailModulAkce: sestavi data pro vypis detailu + urci sablonu, kterou ma LogListConstrol vykreslit
 */
class LogDetailService
{
    /**
     * @var LinkGenerator
     */
    private $linkGenerator;

    /**
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(LinkGenerator $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @param array $logItem
     * @return string
     */
    public function getLinkUserEdit($logItem)
    {
        return $this->getStandardLink($logItem);
    }

    /**
     * @param array $logItem
     * @return string
     */
    public function getLinkUserCreate($logItem)
    {
        return $this->getStandardLink($logItem);
    }

    /**
     * @param array $logItem
     * @return string
     */
    public function getLinkImportAutomaticRun($logItem)
    {
        return $this->getImportLogLink($logItem);
    }

    /**
     * @param array $logItem
     * @return string
     */
    public function getLinkImportManual($logItem)
    {
        return $this->getImportLogLink($logItem);
    }

    /**
     * @param array $logItem
     * @return string
     */
    public function getLinkUserLogin($logItem)
    {
        return $this->getStandardLink($logItem);
    }

    /**
     * @param array $logItem
     * @return array
     */
    public function getDetailUserEdit($logItem)
    {
        return $this->getUserSavedData($logItem);
    }

    /**
     * @param array $logItem
     * @return array
     */
    public function getDetailUserCreate($logItem)
    {
        return $this->getUserSavedData($logItem);
    }

    /**
     * @param array $logItem
     * @return array
     */
    public function getDetailUserLogin($logItem)
    {
        return [
            'type' => '.user.login',
            'item' => $logItem,
            'data' => Json::decode($logItem['custom_data'], Json::FORCE_ARRAY),
        ];
    }

    /**
     * @param array $logItem
     * @return string
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    protected function getStandardLink($logItem)
    {
        return $this->linkGenerator->link('Admin:System:default', ['id' => $logItem['id']]);
    }

    /**
     * Log ulozenych zmen u uzivatele
     * @param $logItem
     * @return array
     */
    protected function getUserSavedData($logItem)
    {
        return [
            'type' => '.user.data',
            'item' => $logItem,
            'data' => Arrays::get(Json::decode($logItem['custom_data'], Json::FORCE_ARRAY), 'data', []),
        ];
    }

    /**
     * Vychozi log
     * @param $logItem
     * @return array
     */
    public function getStandardDetail($logItem)
    {
        return [
            'type' => '.detail',
            'item' => $logItem,
        ];
    }

    /**
     * @param array $logItem
     * @return null|string
     */
    protected function getImportLogLink($logItem)
    {
        $ret = null;

        $logId = Arrays::get(Json::decode($logItem['custom_data'], Json::FORCE_ARRAY), 'logId', null);

        if ($logId) {
            $ret = $this->linkGenerator->link('Admin:Import:logs', ['id' => $logId]);
        }

        return $ret;
    }
}
