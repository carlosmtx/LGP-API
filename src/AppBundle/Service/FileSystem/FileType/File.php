<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:12
 */

namespace AppBundle\Service\FileSystem\FileType;


use AppBundle\Service\FileSystem\FileFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Intl\Exception\NotImplementedException;

abstract class File{

    const ZIP = 'ZIP';
    const RAR = 'RAR';
    const REGULAR = 'REGULAR';
    const FOLDER  = 'FOLDER';

    /** @var  $fs Filesystem */
    protected $fs;              #The FileSystemComponent
    protected $srcPath;         #The Path to the file
    protected $tmpDir;          #Temporary Directory
    /** @var  FileFactory */
    protected $fileFactory;


    protected $treeStructure = [];
    /** @var  File[] */
    protected $children;

    /**
     * @param $path
     * @param $fs
     * @param $tmpDir
     * @param $fileFactory
     */
    public function __construct($path,$fs,$tmpDir,$fileFactory){
        $this->srcPath = $path;
        $this->fs = $fs;
        $this->tmpDir = $tmpDir;
        $this->fileFactory = $fileFactory;
    }


    public function move($path){
        $this->fs->copy($this->srcPath,$path);
        $this->remove($path);
    }

    function compressTo($type = File::ZIP,$dest = false){
        $dest = $dest ?: $this->tmpDir;
        switch($type){
            case File::ZIP :
                return new ZipFile($this->filePath,$this->fs,$this->tmpDir,$this->fileFactory);
                break;
            case File::RAR :
                throw new NotImplementedException("Rar file compression not yet implemented");
                break;
        }
        throw new \InvalidArgumentException("Type specified was not valid");
    }

    abstract function remove();
    abstract protected function getChildren();
    function getTree(){
//        if ( $this->getChildren()){
//            $tree = [];
//            /** @var File $child */
//            foreach($this->getChildren() as $child){
//                $tree[] = $child->getTree();
//            }
//            return $tree;
//        }
//        return $this;
    }
}