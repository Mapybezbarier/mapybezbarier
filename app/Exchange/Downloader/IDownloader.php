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
     * @return string
     */
    public function getData($importItem);
}
