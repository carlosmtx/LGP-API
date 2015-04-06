<?php
/**
 * User: carlos
 * Date: 06/04/2015
 * Time: 00:41
 */

namespace AppBundle\Service\EventSubscriber;


use AppBundle\Document\Version\Version;
use AppBundle\Service\FileSystem\FileSystem;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

class VersionSubscriber implements EventSubscriber{

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postRemove'
        ];
    }


    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(FileSystem $fileSystem){
        $this->fileSystem = $fileSystem;
    }
    public function isChannel($document){
        return is_a($document,Version::class);
    }

    public function postPersist(LifecycleEventArgs $args){
        /** @var Version $version */
        $version = $args->getDocument();
        if( ! $this->isChannel($version)){
            return;
        }
        $this->fileSystem->createVersionDir($version->getChannel()->getName() ,$version->getName());
    }

    public function postRemove(LifecycleEventArgs $args){
        /** @var Version $version */
        $version = $args->getDocument();
        if( !$this->isChannel($version)){
            return;
        }
        $this->fileSystem->removeVersionDir($version->getChannel()->getName() ,$version->getName());


    }
}