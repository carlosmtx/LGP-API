<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:18
 */

namespace AppBundle\Service\FileSystem\FileType;


use AppBundle\Service\FileSystem\FileType\Compressed\Compressed;

class RarFile extends Compressed{

    function save($path=false)
    {
        // TODO: Implement save() method.
    }
    function remove($path = false)
    {
        $this->fs->remove($this->filePath);
    }

    function toFile()
    {
        // TODO: Implement toFile() method.
    }

    /**
     * @param bool $dest
     * @return FolderFile
     */
    public function extractTo($dest = false)
    {
        // TODO: Implement extractTo() method.
    }
}