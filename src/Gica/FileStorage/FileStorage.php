<?php
/******************************************************************************
 * Copyright (c) 2017 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\FileStorage;

interface FileStorage
{
    public function moveUploadedFileToStorage($itemId, \Psr\Http\Message\UploadedFileInterface $uploadedFile);

    public function putItemToStorageByContents($itemId, $contents);

    public function putItemToStorageByStream($itemId, \Psr\Http\Message\StreamInterface $stream);

    public function deleteItem($itemId);

    public function cloneItem($sourceId, $destinationId);

    public function changeItemOwnerDeep($itemId, $owner);

    public function hasItem($itemId);

    public function loadItemContentsById($itemId);

    public function getItemStream($itemId):\Psr\Http\Message\StreamInterface;

    public function getItemSize($itemId);

    public function factoryItem($itemId):\Gica\FileStorage\FileItem;

    public function getItemHash($itemId);

    public function outputItem($itemId);
}