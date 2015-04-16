<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 15:17
 */

namespace AppBundle\Subscriber;


use AppBundle\Event\Channel\ChannelCreationEvent;
use AppBundle\Event\Channel\ChannelDeleteEvent;
use AppBundle\Event\Channel\ChannelEvent;
use AppBundle\Service\FileSystem\FileManager;
use AppBundle\Service\FileSystem\FileSystem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChannelSubscriber implements EventSubscriberInterface{
    /**
     * @var FileManager
     */
    private $fs;


    public static
    function getSubscribedEvents()
    {
        return[
            ChannelEvent::ChannelCreation => 'onChannelCreate',
            ChannelEvent::ChannelDelete  => 'onChannelDelete'
        ];
    }

    public function onChannelCreate(ChannelCreationEvent $event){
        $this->fs->createChannel($event->getChannel());
    }

    public function onChannelDelete(ChannelDeleteEvent $event){
        $this->fs->removeChannel($event->getChannel());
    }

    public function __construct(FileManager $fs){

        $this->fs = $fs;
    }
}