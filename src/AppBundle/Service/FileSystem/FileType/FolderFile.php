<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 16/04/2015
 * Time: 22:00
 */

namespace AppBundle\Service\FileSystem\FileType;


class FolderFile extends AbstractFile{

    function save($path = false)
    {
        $this->fs->mkdir($path ?: $this->filePath);
    }

    function remove($path = false)
    {
        $this->fs->remove($this->filePath);
    }

    function toFile()
    {
        // TODO: Implement toFile() method.
    }
}