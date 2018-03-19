<?php

namespace MP\Exchange\Downloader;

use MP\Exchange\Exception\DownloadException;
use MP\Util\Strings;
use Nette\Http\Url;
use Nette\Utils\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Downloader pro data z GIS Brno (http://gis.brno.cz/arcgis/rest/services/PUBLIC/bezbarierove_objekty/MapServer)
 * Data jsou dostupna pres HTTP, ale kazdy typ objektu zvlast
 * @author Jakub Vrbas
 */
class BrnoGisDownloader implements IDownloader
{
    protected $importLayers = [
        1, //Kultura
        2, //Úřady
        3, //Finance
        4, //Obchody
        5, //Restaurace, kavárny
        6, //Ubytování
        7, //Sport a volný čas
        8, //Zdravotnictví
        //9, //WC - neimportuje se, obsahuje duplicity s objekty z ostatnich vrstev
        //10, //Family pointy - neimportuje se
        22, //Terminál hromadné dopravy
    ];

    /**
     * Kazda vrstva (typ objektu) ma vlastni URL
     * Postupne je potreba spojit vsechny pozadovane vrstvy
     *
     * @param array $importItem
     * @return array
     * @throws DownloadException
     */
    public function getData($importItem)
    {
        $ret = [];

        $baseUrl = Arrays::get($importItem, 'url', null);

        if ($baseUrl) {
            foreach($this->importLayers as $layerId) {
                $layerUrl = new Url($baseUrl);
                $path = $layerUrl->getPath();
                $layerPath = Strings::replace($path, "~MapServer/(\d+)/~", "MapServer/{$layerId}/", 1);

                if ($layerPath) {
                    $layerUrl->setPath($layerPath);
                } else {
                    throw new DownloadException('invalidInputUrl');
                }

                $ret = array_merge($ret, $this->getGisContent($layerUrl));
            }
        } else {
            throw new DownloadException('invalidObjectExternalData');
        }

        return $ret;
    }

    /**
     * Stahne data o jedne vrstve
     * @param Url $layerUrl
     * @return array
     */
    protected function getGisContent($layerUrl)
    {
        $ret = [];

        $fileContent = @file_get_contents($layerUrl);

        try {
            $rawArray = Json::decode($fileContent, Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            throw new DownloadException('getContentFailed');
        }

        if (isset($rawArray['features'])) {
            $ret = $rawArray['features'];
        } else {
            throw new DownloadException('getContentFailed');
        }

        return $ret;
    }
}
