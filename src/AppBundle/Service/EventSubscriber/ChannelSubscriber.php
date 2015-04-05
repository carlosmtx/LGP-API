<?php
/**
 * User: carlos
 * Date: 05/04/2015
 * Time: 16:34
 */

namespace AppBundle\Service\EventSubscriber;


use AppBundle\Document\Channel\Channel;
use AppBundle\Service\FileSystem\FileSystem;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

class ChannelSubscriber  implements EventSubscriber{

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
        return is_a($document,Channel::class);
    }

    public function postPersist(LifecycleEventArgs $args){
        /** @var Channel $channel */
        $channel = $args->getDocument();
        if( ! $this->isChannel($channel)){
           return;
        }
        $this->fileSystem->createChannelDir($channel->getName());
    }

    public function postRemove(LifecycleEventArgs $args){
        /** @var Channel $channel */
        $channel = $args->getDocument();
        if( !$this->isChannel($channel)){
            return;
        }
        $this->fileSystem->removeChannelDir($channel->getName());

    }

}