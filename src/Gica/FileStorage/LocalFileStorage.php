<?php
/******************************************************************************
 * Copyright (c) 2017 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\FileStorage;


use Gica\FileStorage;
use Gica\FileStorage\FilePathGenerator;
use Gica\FileSystem;
use Gica\FileSystem\FileSystemInterface;

class LocalFileStorage implements FileStorage\FileStorage
{
    protected $rootPath;
    /**
     * @var FileSystemInterface
     */
    protected $fileSystem;
    /**
     * @var FilePathGenerator
     */
    private $filePathGenerator;

    public function __construct(
        $rootPath,
        FileSystemInterface $fileSystem,
        FilePathGenerator $filePathGenerator
    )
    {
        $this->rootPath = $rootPath;
        $this->filePathGenerator = $filePathGenerator;
        $this->fileSystem = $fileSystem;
    }

    public function moveUploadedFileToStorage($itemId, \Psr\Http\Message\UploadedFileInterface $uploadedFile)
    {
        $fullStoragePath = $this->getItemFilePath($itemId);

        $this->ensureDirectoryExists(dirname($fullStoragePath));

        $uploadedFile->moveTo($fullStoragePath);

        $this->fileSystem->fileSetPermissions($fullStoragePath, $this->getDefaultDirPermissions());
    }

    protected function relativeToAbsolutePath($relativePath)
    {
        return rtrim($this->getRootRealPath(), '/') . '/' . ltrim($relativePath, '/');
    }

    protected function getRootRealPath()
    {
        return $this->fileSystem->realPath($this->rootPath);
    }

    protected function ensureDirectoryExists($fullDirPath)
    {
        if (!$this->fileSystem->isDirectory($fullDirPath)) {
            $this->fileSystem->makeDirectory($fullDirPath, $this->getDefaultDirPermissions(), true);
        }
    }

    protected function getDefaultDirPermissions()
    {
        return 0777;
    }

    public function putItemToStorageByContents($itemId, $contents)
    {
        $fullStoragePath = $this->getItemFilePath($itemId);

        $this->ensureDirectoryExists(dirname($fullStoragePath));

        $this->fileSystem->filePutContents($fullStoragePath, $contents);

        $this->fileSystem->fileSetPermissions($fullStoragePath, $this->getDefaultDirPermissions());
    }

    public function putItemToStorageByStream($itemId, \Psr\Http\Message\StreamInterface $stream)
    {
        $fullStoragePath = $this->getItemFilePath($itemId);

        $this->ensureDirectoryExists(dirname($fullStoragePath));

        $this->fileSystem->fileWriteStream($fullStoragePath, $stream);

        $this->fileSystem->fileSetPermissions($fullStoragePath, $this->getDefaultDirPermissions());
    }

    public function deleteItem($itemId)
    {
        $this->fileSystem->fileDelete($this->getItemFilePath($itemId));
    }

    public function hasItem($itemId)
    {
        return $this->fileSystem->fileExists($this->getItemFilePath($itemId));
    }

    public function loadItemContentsById($itemId)
    {
        $fullStoragePath = $this->getItemFilePath($itemId);

        return $this->fileSystem->fileGetContents($fullStoragePath);
    }

    public function changeItemOwnerDeep($itemId, $owner)
    {
        $fullStoragePath = $this->getItemFilePath($itemId);

        $this->fileSystem->fileSetOwnerRecursive($this->getRootRealPath(), $fullStoragePath, $owner);
    }

    protected function getItemOwner($itemId)
    {
        return $this->fileSystem->fileGetOwner($this->getItemFilePath($itemId));
    }

    protected function setItemOwner($itemId, $owner)
    {
        return $this->fileSystem->fileSetOwner($this->getItemFilePath($itemId), $owner);
    }

    /**
     * @param $itemId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getItemStream($itemId):\Psr\Http\Message\StreamInterface
    {
        return $this->fileSystem->fileGetStream($this->getItemFilePath($itemId), 'a+');
    }

    public function getItemSize($itemId)
    {
        return $this->fileSystem->fileGetSize($this->getItemFilePath($itemId));
    }

    public function factoryItem($itemId):\Gica\FileStorage\FileItem
    {
        return new \Gica\FileStorage\FileItem($this, $itemId);
    }

    public function getItemHash($itemId)
    {
        return $this->fileSystem->md5File($this->getItemFilePath($itemId));
    }

    private function getItemFilePath($itemId):string
    {
        /** @todo remove this if! */
        if (false !== stripos($itemId, '/')) {
            return $this->relativeToAbsolutePath($itemId);
        }

        return $this->relativeToAbsolutePath($this->filePathGenerator->generateFilePath($itemId));
    }

    public function outputItem($itemId)
    {
        $this->fileSystem->fileOutput($this->getItemFilePath($itemId));
    }

    public function cloneItem($sourceId, $destinationId)
    {
        $srcPath = $this->getItemFilePath($sourceId);
        $dstPath = $this->getItemFilePath($destinationId);

        $this->ensureDirectoryExists(dirname($dstPath));

        $this->fileSystem->fileCopy($srcPath, $dstPath);
    }
}