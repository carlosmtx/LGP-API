<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:18
 */

namespace AppBundle\Service\FileSystem\FileType;

use Symfony\Component\HttpFoundation\File\File;

class ZipFile extends AbstractFile{


    function __construct($path,$fs,$tmpDir){
        parent::__construct($path,$fs,$tmpDir);
    }

    function save($path = false)
    {
        $zipper = new \ZipArchive();
        $zipper->open($this->filePath);
        $zipper->extractTo($path);
    }

    function remove($path = false)
    {
        $this->fs->remove($this->filePath);
    }

    function toFile()
    {
        $destPath = $this->tmpDir.'/'.time().'.zip';
        exec("cd {$this->filePath} && zip -r $destPath *");
        return new File($destPath);
    }
}