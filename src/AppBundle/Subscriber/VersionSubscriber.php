<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 16:12
 */

namespace AppBundle\Subscriber;


use AppBundle\Event\Version\VersionCreationEvent;
use AppBundle\Event\Version\VersionDeleteEvent;
use AppBundle\Event\Version\VersionEvent;
use AppBundle\Service\FileSystem\FileSystem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VersionSubscriber implements EventSubscriberInterface {
    /**
     * @var FileSystem
     */
    private $fs;

    public static function getSubscribedEvents()
    {
        return[
            VersionEvent::VersionCreation => 'onVersionCreate',
            VersionEvent::VersionDelete   => 'onVersionDelete'
        ];
    }

    public function onVersionCreate(VersionCreationEvent $event){
        $this->fs->createVersion($event->getVersion());
    }

    public function onVersionDelete(VersionDeleteEvent $event){
        $this->fs->removeVersion($event->getVersion());
    }

    public function __construct(FileSystem $fs){

        $this->fs = $fs;
    }
}