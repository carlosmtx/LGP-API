<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 16/04/2015
 * Time: 22:00
 */

namespace AppBundle\Service\FileSystem\FileType;


class FolderFile extends File{

    function remove()
    {
        $this->fs->remove($this->filePath);
    }

    protected function getChildren()
    {
        return [];
    }
}