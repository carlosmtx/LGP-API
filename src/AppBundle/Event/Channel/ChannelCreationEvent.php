<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 14:57
 */

namespace AppBundle\Event\Channel;
use AppBundle\Document\Channel\Channel;
use Symfony\Component\EventDispatcher\Event;

class ChannelCreationEvent extends Event{

    /**
     * @var Channel
     */
    private $channel;

    public function __construct(Channel $channel){

        $this->channel = $channel;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }


}