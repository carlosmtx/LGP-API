<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 16/04/2015
 * Time: 14:32
 */

namespace AppBundle\Subscriber;


use AppBundle\Event\File\FileCreationEvent;
use AppBundle\Event\File\FileEvent;
use AppBundle\Service\FileSystem\FileManager;
use AppBundle\Service\FileSystem\FileSystem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FileSubscriber implements EventSubscriberInterface{

    /**
     * @var FileSystem
     */
    private $fs;

    public function __construct(FileManager $fs){
        $this->fs = $fs;
    }


    public static function getSubscribedEvents()
    {
        return [
            FileEvent::FileCreation => 'onFileCreation'
        ];
    }

    public function onFileCreation(FileCreationEvent $fileEvent){
        $fileDoc = $fileEvent->getFileDocument();
        $fileFile= $fileEvent->getUploadedFile();

        $this->fs->createFile($fileDoc,$fileFile);


    }
}