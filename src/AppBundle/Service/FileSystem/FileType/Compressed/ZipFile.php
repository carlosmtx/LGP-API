<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:18
 */

namespace AppBundle\Service\FileSystem\FileType;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ZipFile extends AbstractFile{
    private $extracted = false;
    /** @var Filesystem $fs */
    function __construct($path,$fs,$tmpDir){
        parent::__construct($path,$fs,$tmpDir);
        $this->extracted = false;
    }
    function extract(){

    }
    function save($path = false)
    {
        $zipper = new \ZipArchive();
        $zipper->open($this->filePath);
        $zipper->extractTo($path);

    }

    function compress()
    {
        $destPath = $this->tmpDir.'/'.time().'.zip';
        exec("cd {$this->filePath} && zip -r $destPath *");
        return new File($destPath);
    }

    function remove($path = false)
    {
        $this->fs->remove($this->filePath);
    }

    protected function getStructure()
    {
        // TODO: Implement getStructure() method.
    }

    function toFile()
    {
        // TODO: Implement toFile() method.
    }

    protected function getChildren()
    {
        // TODO: Implement getChildren() method.
    }
}