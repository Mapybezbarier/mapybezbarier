<?php

namespace MP\Exchange\Downloader;

use MP\Exchange\Exception\DownloadException;
use MP\Util\Lang\Lang;
use Nette\Http\Url;
use Nette\Utils\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Downloader pro data z Wheelmap (https://www.wheelmap.org)
 * Data jsou dostupna pres HTTP, ale jsou strankovana
 * @author Jakub Vrbas
 */
class WheelmapDownloader implements IDownloader
{
    /** @var Lang */
    protected $lang;

    /**
     * @param Lang $lang
     */
    public function __construct(Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * Kazda stranka s daty ma meta informace o celkovem poctu a aktualni strane
     * Postupne je potreba spojit 3 zdroje: wheelchair=yes/limited/no, objekty bez udane pristupnosti nestahujeme
     *
     * @param array $importItem
     * @return array Krome dat o objektech ['nodes'] vraci interni typy ['node_types']
     * @throws DownloadException
     */
    public function getData($importItem)
    {
        set_time_limit(120);

        $nodes = [];

        $baseUrl = Arrays::get($importItem, 'url', null);

        if ($baseUrl) {
            foreach(['yes', 'limited', 'no'] as $wheelchairAccessibility) {
                $accessibilityUrl = new Url($baseUrl);
                $accessibilityUrl->setQueryParameter('wheelchair', $wheelchairAccessibility);

                $nodes = array_merge($nodes, $this->getWheelmapContent($accessibilityUrl, 'nodes'));
            }

            $nodeTypesUrl = $this->prepareNodeTypesUrl($baseUrl);
            $nodeTypes = $this->getWheelmapContent($nodeTypesUrl, 'node_types');
        } else {
            throw new DownloadException('invalidObjectExternalData');
        }

        return [
            'nodes' => $nodes,
            'node_types' => $nodeTypes,
        ];
    }

    /**
     * Nacte z metadat hodnotu atributu
     * @param array $data
     * @param string $attrName
     * @param mixed $default
     */
    protected function getMetadataInfo($data, $attrName, $default)
    {
        $ret = $default;

        if (!empty($data['meta'])) {
            $ret = Arrays::get($data['meta'], $attrName, $default);
        }

        return $ret;
    }

    /**
     * Sestavi URL hledane stranky, stahne data a vrati data
     * @param Url $baseUrl
     * @param integer $page
     * @return array
     */
    protected function getPageData($url, $page, $contentKey)
    {
        $url->setQueryParameter('page', $page);
        $data = $this->getDataFromJson($url);

        return Arrays::get($data, $contentKey, []);
    }

    /**
     * Stahne soubor pres HTTP a rozparsuje JSON
     * @param Url $url
     * @return array
     * @throws DownloadException
     */
    protected function getDataFromJson($url)
    {
        $ret = [];

        $fileContent = @file_get_contents($url);

        try {
            $ret = Json::decode($fileContent, Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            throw new DownloadException('getContentFailed');
        }

        return $ret;
    }

    /**
     * Stahne a rozparsuje strankovana data ke konkretnimu uzlu
     * @param Url $contentUrl
     * @param string $contentKey
     * @return array
     */
    protected function getWheelmapContent($contentUrl, $contentKey)
    {
        $ret = [];

        $data = $this->getDataFromJson($contentUrl);

        $actualPage = $this->getMetadataInfo($data, 'page', 1);
        $totalPages = $this->getMetadataInfo($data, 'num_pages', 1);

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page == $actualPage) {
                $ret = array_merge($ret, Arrays::get($data, $contentKey, []));
            } else {
                $ret = array_merge($ret, $this->getPageData($contentUrl, $page, $contentKey));
            }
        }

        return $ret;
    }

    /**
     * Z originalni URL si vezme zaklad (domenu a klic) a upravi pro dotaz na typy
     * @param string $baseUrl
     * @return Url
     */
    protected function prepareNodeTypesUrl($baseUrl)
    {
        $ret = new Url($baseUrl);
        $path = $ret->getPath();
        $ret->setPath(str_replace('nodes', 'node_types', $path));
        $ret->setQueryParameter('bbox', null);
        $ret->setQueryParameter('locale', $this->lang->getLang());

        return $ret;
    }
}
