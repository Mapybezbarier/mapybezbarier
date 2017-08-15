<?php

namespace MP\Util;

use php_user_filter;
use RuntimeException;

/**
 * Kopie Goodby\CSV\Import\Standard\StreamFilter\ConvertMbstringEncoding s prevodme kodovani pomoci iconv
 * mb_convert_encoding neumi kodovani CP1250
 *
 * Neresi pripadne chybne znaky
 */
class ConvertIconvEncoding extends php_user_filter
{
    /**
     * @var string
     */
    const FILTER_NAMESPACE = 'convert.iconv.encoding.';

    /**
     * @var bool
     */
    private static $hasBeenRegistered = false;

    /**
     * @var string
     */
    private $fromCharset;

    /**
     * @var string
     */
    private $toCharset;

    /**
     * Return filter name
     * @return string
     */
    public static function getFilterName()
    {
        return self::FILTER_NAMESPACE.'*';
    }

    /**
     * Register this class as a stream filter
     * @throws \RuntimeException
     */
    public static function register()
    {
        if ( self::$hasBeenRegistered === true ) {
            return;
        }

        if ( stream_filter_register(self::getFilterName(), __CLASS__) === false ) {
            throw new RuntimeException('Failed to register stream filter: '.self::getFilterName());
        }

        self::$hasBeenRegistered = true;
    }

    /**
     * Return filter URL
     * @param string $filename
     * @param string $fromCharset
     * @param string $toCharset
     * @return string
     */
    public static function getFilterURL($filename, $fromCharset, $toCharset = null)
    {
        if ( $toCharset === null ) {
            return sprintf('php://filter/' . self::FILTER_NAMESPACE . '%s/resource=%s', $fromCharset, $filename);
        } else {
            return sprintf('php://filter/' . self::FILTER_NAMESPACE . '%s:%s/resource=%s', $fromCharset, $toCharset, $filename);
        }
    }

    /**
     * @return bool
     */
    public function onCreate()
    {
        if ( strpos($this->filtername, self::FILTER_NAMESPACE) !== 0 ) {
            return false;
        }

        $parameterString = substr($this->filtername, strlen(self::FILTER_NAMESPACE));

        if ( ! preg_match('/^(?P<from>[-\w]+)(:(?P<to>[-\w]+))?$/', $parameterString, $matches) ) {
            return false;
        }

        $this->fromCharset = $matches['from'] ?? 'CP1250';
        $this->toCharset   = $matches['to'] ?? 'UTF-8';

        return true;
    }

    /**
     * @param string $in
     * @param string $out
     * @param string $consumed
     * @param $closing
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ( $bucket = stream_bucket_make_writeable($in) ) {
            $bucket->data = iconv($this->fromCharset, $this->toCharset, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}
