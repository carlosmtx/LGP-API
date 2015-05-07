<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel;
use AppBundle\Event\Channel\ChannelCreationEvent;
use AppBundle\Event\Channel\ChannelEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelController extends Controller
{
    public function createChannelAction(Request $request,$cname)
    {
        $dm =     $this->get('doctrine_mongodb')->getManager();

        $channel = new Channel();
        $channel->name = $cname;
        $channel->description = $request->request->get('description','');

        if(!$channel){
            return new Response("The channel $cname was not found",400);
        }

        $event = new ChannelCreationEvent($channel);
        $this->get('event_dispatcher')->dispatch(ChannelEvent::ChannelCreation,$event);

        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse($channel);
    }


}