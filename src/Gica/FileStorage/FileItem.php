<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\FileStorage;


class FileItem
{

    /**
     * @var \Gica\FileStorage\FileStorage
     */
    private $fileStorage;
    private $fileId;

    public function __construct(
        \Gica\FileStorage\FileStorage $fileStorage,
        $fileId)
    {
        $this->fileStorage = $fileStorage;
        $this->fileId = $fileId;
    }

    public function getFileContents()
    {
        return $this->getFileStream()->getContents();
    }

    public function getFileStream()
    {
        return $this->fileStorage->getItemStream($this->fileId);
    }

    public function getMimeType()
    {
        $finfo = new \finfo(FILEINFO_MIME);
        list($contentType,) = explode(';', $finfo->buffer($this->getFileContents()));
        return $contentType;
        //alternative: mime_content_type on temporary file with content from $this->getAttachedFileContents()
    }

    public function getFileSize()
    {
        return $this->fileStorage->getItemSize($this->fileId);

    }

    public function getFileHash()
    {
        return $this->fileStorage->getItemHash($this->fileId);
    }
}