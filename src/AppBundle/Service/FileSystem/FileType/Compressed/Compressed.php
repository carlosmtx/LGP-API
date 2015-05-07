<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 24/04/2015
 * Time: 18:24
 */

namespace AppBundle\Service\FileSystem\FileType\Compressed;



use AppBundle\Service\FileSystem\FileType\File;
use AppBundle\Service\FileSystem\FileType\FolderFile;

abstract class Compressed extends File{
    protected $decompressed;
    protected $fileDecompressTmpDir;

    public function __construct($path,$fs,$tmpDir,$fileFactory){
        parent::__construct($path,$fs,$tmpDir,$fileFactory);
        $this->decompressed = false;

    }
    public function __destruct(){
        //$this->fs->remove($this->fileDecompressTmpDir);
    }

    protected function getChildren()
    {
//        $dir = $this->extract();
//        return $this->fileFactory->get($dir)->getChildren();
    }

    function remove()
    {

    }

    function move($path)
    {

    }

    /**
     * @param bool $dest
     * @return FolderFile
     */
    abstract public function extractTo($dest=false);

}