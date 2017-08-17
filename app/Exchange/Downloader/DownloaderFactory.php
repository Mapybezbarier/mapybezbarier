<?php

namespace MP\Exchange\Downloader;

use Nette\DI\Container;

/**
 * Tovarna na sluzby pro stahovani dat dle typu zdroje
 * Slouzi pro zdroje, pro ktere nejsou data prosutpna ciste pres HTTP request
 */
class DownloaderFactory
{
    /** @var Container */
    protected $context;

    /** @var array */
    protected $mapping;

    /**
     * @param Container $context
     * @param array $mapping
     */
    public function __construct(Container $context, array $mapping)
    {
        $this->context = $context;
        $this->mapping = $mapping;
    }

    /**
     * @param array $source
     *
     * @return IDownloader
     */
    public function create($source)
    {
        $downloader = null;

        if (isset($this->mapping[$source['format']])) {
            $downloader = $this->context->getService($this->mapping[$source['format']]);
        }

        return $downloader;
    }
}
