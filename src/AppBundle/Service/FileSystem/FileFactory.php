<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:07
 */

namespace AppBundle\Service\FileSystem;


use AppBundle\Document\File\File;
use AppBundle\Service\FileSystem\FileType\AbstractFile;
use AppBundle\Service\FileSystem\FileType\RarFile;
use AppBundle\Service\FileSystem\FileType\RegularFile;
use AppBundle\Service\FileSystem\FileType\ZipFile;

class FileFactory {
    const ZIP = "zip";
    const RAR = "rar";

    /**
     * @param File $file
     * @return AbstractFile
     */
    public function get(File $file){
        switch( $file->getFile()->getExtension() ){
            case FileFactory::ZIP :
                $fileObj = new ZipFile();
                break;
            case FileFactory::RAR :
                $fileObj = new RarFile();
                break;
            default:
                $fileObj = new RegularFile();
                break;
        }
        return $fileObj;


    }

}