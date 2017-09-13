<?php
namespace MP\Exchange\Downloader;

/**
 * Rozhrani pro Downloader
 */
interface IDownloader
{
    /**
     * Vrati data pro importni zdroj
     *
     * @param array $importItem
     * @return mixed Data ve formatu, ktery je podporovan prislusnym parserem
     */
    public function getData($importItem);
}
