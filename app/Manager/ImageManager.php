<?php

namespace MP\Manager;

use MP\Service\ImageFileStorage;
use MP\Util\Arrays;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

/**
 * Manazer pro spravu fotek objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ImageManager
{
    /** @const Nazvy namespace */
    const NAMESPACE_DRAFT = 'draft',
        NAMESPACE_OBJECT = 'object';

    /** @var ImageFileStorage */
    protected $imageStorage;

    /** @var array */
    private static $types = [
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/tiff' => 'tif',
    ];

    /**
     * @param ImageFileStorage $imageStorage
     */
    public function __construct(ImageFileStorage $imageStorage)
    {
        $this->imageStorage = $imageStorage;
    }

    /**
     * Ulozi fotku
     *
     * @param FileUpload|null $file
     * @param int $id
     * @param string $namespace
     */
    public function persist(FileUpload $file, $id, $namespace)
    {
        if ($file && $file->isOk() && $file->isImage()) {
            $this->remove($id, $namespace);

            $filename = "{$this->getStorageDir($namespace)}/{$id}.{$this->getExtension($file->getTemporaryFile())}";

            $file->move($filename);
        }
    }

    /**
     * Publikuje fotku z draftu do objektu
     *
     * @param int $draft
     * @param int $object
     */
    public function publish($draft, $object)
    {
        /** @var \SplFileInfo $sourceFileInfo */
        foreach (Finder::findFiles("{$draft}.*")->from($this->getStorageDir(self::NAMESPACE_DRAFT)) as $sourceFileInfo) {
            $destinationPathname = $this->getStorageDir(self::NAMESPACE_OBJECT) . "/{$object}.{$sourceFileInfo->getExtension()}";

            if (is_file($destinationPathname)) {
                $destinationFileInfo = new \SplFileInfo($destinationPathname);

                if ($sourceFileInfo->getMTime() > $destinationFileInfo->getMTime()) {
                    FileSystem::copy($sourceFileInfo->getPathname(), $destinationFileInfo->getPathname());
                }
            } else {
                FileSystem::copy($sourceFileInfo->getPathname(), $destinationPathname);
            }
        }
    }

    /**
     * Zkopiruje fotku objektu
     *
     * @param int $source
     * @param int $destination
     */
    public function copy($source, $destination)
    {
        /** @var \SplFileInfo $sourceFileInfo */
        foreach (Finder::findFiles("{$source}.*")->from($this->getStorageDir(self::NAMESPACE_OBJECT)) as $sourceFileInfo) {
            $destinationPathname = $this->getStorageDir(self::NAMESPACE_OBJECT) . "/{$destination}.{$sourceFileInfo->getExtension()}";

            FileSystem::copy($sourceFileInfo->getPathname(), $destinationPathname);
        }
    }

    /**
     * @param int $id
     * @param string $namespace
     */
    public function remove($id, $namespace)
    {
        $this->imageStorage->setNamespace("images/{$namespace}");

        /** @var \SplFileInfo $image */
        foreach (Finder::findFiles("{$id}.*")->from($this->getStorageDir($namespace)) as $image) {
            $this->imageStorage->delete($image->getFilename());
        }
    }

    /**
     * Vyhleda fotku objektu
     *
     * @param int $id
     * @param string $namespace
     *
     * @return string|null
     */
    public function find($id, $namespace)
    {
        $image = null;

        /** @var \SplFileInfo $image */
        foreach (Finder::findFiles("{$id}.*")->from($this->getStorageDir($namespace)) as $image) {
            $image = "images/{$namespace}/" . $image->getFilename();

            break;
        }

        return $image;
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    private function getStorageDir($namespace)
    {
        $directory = STORAGE_DIR . "/images/{$namespace}";

        return $directory;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function getExtension($file)
    {
        $type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        $type = strpos($type, '/') ? $type : 'application/octet-stream';

        return Arrays::get(self::$types, $type, 'bin');
    }
}
