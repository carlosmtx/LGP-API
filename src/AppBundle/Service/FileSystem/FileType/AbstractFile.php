<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:12
 */

namespace AppBundle\Service\FileSystem\FileType;


use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractFile {

    /** @var  $fs Filesystem */
    protected $fs;
    protected $filePath;
    protected $rootDir;
    protected $tmpDir;

    public function __construct($path,$fs,$tmpDir){
        $this->filePath = $path;
        $this->fs = $fs;
        $this->tmpDir = $tmpDir;
    }
    public function move($path){
        $this->remove($path);
        $this->save($path);
    }
    abstract function save($path = false);
    abstract function remove($path = false);
    abstract function toFile();


}