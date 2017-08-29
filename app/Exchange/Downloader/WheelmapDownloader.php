<?php

namespace MP\Exchange\Downloader;

use MP\Exchange\Exception\DownloadException;
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
    /**
     * Kazda stranka s daty ma meta informace o celkovem poctu a aktualni strane
     * Postupne je potreba spojit 3 zdroje: wheelchair=yes/limited/no, objekty bez udane pristupnosti nestahujeme
     *
     * @param array $importItem
     * @return array
     * @throws DownloadException
     */
    public function getData($importItem)
    {
        set_time_limit(120);

        $ret = [];

        $baseUrl = Arrays::get($importItem, 'url', null);

        if ($baseUrl) {
            foreach(['yes', 'limited', 'no'] as $wheelchairAccessibility) {
                $accessibilityUrl = new Url($baseUrl);
                $accessibilityUrl->setQueryParameter('wheelchair', $wheelchairAccessibility);
                $data = $this->getContent($accessibilityUrl);

                $actualPage = $this->getMetadataInfo($data, 'page', 1);
                $totalPages = $this->getMetadataInfo($data, 'num_pages', 1);

                for ($page = 1; $page <= $totalPages; $page++) {
                    if ($page == $actualPage) {
                        $ret = array_merge($ret, Arrays::get($data, 'nodes', []));
                    } else {
                        $ret = array_merge($ret, $this->getNodesData($accessibilityUrl, $page));
                    }
                }
            }
        } else {
            throw new DownloadException('invalidObjectExternalData');
        }

        return $ret;
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
    protected function getNodesData($url, $page)
    {
        $url->setQueryParameter('page', $page);
        $data = $this->getContent($url);

        return Arrays::get($data, 'nodes', []);
    }

    /**
     * Stahne soubor pres HTTP a rozparsuje JSON
     * @param Url $url
     * @return array
     * @throws DownloadException
     */
    protected function getContent($url)
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

}
