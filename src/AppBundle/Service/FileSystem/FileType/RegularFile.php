<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:19
 */

namespace AppBundle\Service\FileSystem\FileType;


class RegularFile extends File{

    function save($path = false)
    {
       $this->fs->copy($this->filePath,$path);
    }

    function remove($path = false)
    {
        $this->fs->remove($this->filePath);
    }

    protected function getChildren()
    {
        return false;
    }
}