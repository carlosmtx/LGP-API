<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel;
use AppBundle\Event\Channel\ChannelCreationEvent;
use AppBundle\Event\Channel\ChannelEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChannelController extends Controller
{
    public function createChannelAction($cname)
    {
        $dm =     $this->get('doctrine_mongodb')->getManager();

        $channel = new Channel();
        $channel->name = $cname;


        $event = new ChannelCreationEvent($channel);
        $this->get('event_dispatcher')->dispatch(ChannelEvent::ChannelCreation,$event);

        $dm->persist($channel);
        $dm->flush();
        $dir = $this->container->getParameter('upload_root_dir')."/$cname";
        $this->get('file_system_provider')->mkdir($dir);
        return new JsonResponse('OK');
    }
}