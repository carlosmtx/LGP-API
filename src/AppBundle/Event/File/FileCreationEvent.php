<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 16/04/2015
 * Time: 00:20
 */

namespace AppBundle\Event\File;


use AppBundle\Document\File\File;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileCreationEvent extends Event{

    protected $fileDocument;
    protected $uploadedFile;

    /**
     * @param File $file
     * @param UploadedFile $fileupload
     */
    public function __construct(File $file,UploadedFile $fileupload){
        $this->fileDocument = $file;
        $this->uploadedFile= $fileupload;
    }

    /**
     * @return File
     */
    public function getFileDocument()
    {
        return $this->fileDocument;
    }

    /**
     * @param File $fileDocument
     */
    public function setFileDocument($fileDocument)
    {
        $this->fileDocument = $fileDocument;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }


}