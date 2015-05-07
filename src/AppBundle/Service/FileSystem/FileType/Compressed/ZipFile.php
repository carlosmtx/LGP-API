<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:18
 */

namespace AppBundle\Service\FileSystem\FileType\Compressed;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ZipFile extends Compressed{
    private $extracted = false;

    /**
     * @param $path
     * @var Filesystem $fs
     * @param $tmpDir
     * @param $fileFactory
     */
    function __construct($path,$fs,$tmpDir,$fileFactory){
        parent::__construct($path,$fs,$tmpDir,$fileFactory);
        $this->extracted = false;
    }
    function extractTo($path=false){
        $path = $path ? $path : $this->tmpDir;
        $zipper = new \ZipArchive();
        $zipper->open($this->srcPath);
        $zipper->extractTo($path);
        $zipper->close();
    }

    function compress()
    {
        $destPath = $this->tmpDir.'/'.time().'.zip';
        exec("cd {$this->filePath} && zip -r $destPath *");
        return new File($destPath);
    }

    function remove($path = false)
    {
        //$this->fs->remove($this->filePath);
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