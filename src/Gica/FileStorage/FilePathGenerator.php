<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\FileStorage;


class FilePathGenerator
{
    public function generateFilePath($id)
    {
        $id = (string)($id);

        if (false !== stripos('/', $id) || false !== stripos('\\', $id) || false !== stripos('..', $id) || '.' == $id) {
            throw new \InvalidArgumentException("ID contain invalid characters");
        }

        $relativePath = $id;

        $dir1 = substr($id, 0, 2);
        $dir2 = substr($id, 2, 2);

        return $dir1 . '/' . $dir2 . '/' . $relativePath;
    }
}