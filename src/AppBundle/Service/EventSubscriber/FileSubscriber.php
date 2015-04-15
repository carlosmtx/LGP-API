<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 07/04/2015
 * Time: 12:35
 */

namespace AppBundle\Service\EventSubscriber;


use AppBundle\Document\File\File;
use AppBundle\Service\FileSystem\FileSystem;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

class FileSubscriber implements EventSubscriber{


    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postRemove'
        ];
    }
    public function __construct(FileSystem $fileSystem){
        $this->fileSystem = $fileSystem;
    }
    public function isFile($document){
        return is_a($document,File::class);
    }

    public function postPersist(LifecycleEventArgs $args){
        /** @var File $file */
        $file = $args->getDocument();
        if(!$this->isFile($file)){
            return;
        }
        $this->fileSystem->createFile(
            $file->getVersion()->getChannel()->getName(),
            $file->getVersion()->getName(),
            $file->getFile()
        );
    }

    public function postRemove(LifecycleEventArgs $args){
        /** @var File $channel */
        $channel = $args->getDocument();
        if( !$this->isFile($channel)){
            return;
        }
        $this->fileSystem->removeChannelDir($channel->getName());

    }
}