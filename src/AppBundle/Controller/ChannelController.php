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
    public function createChannelAction(Request $request)
    {
        $dm         = $this->get('doctrine_mongodb')->getManager();

        $name       = $request->request->get('name',false);
        $description= $request->request->get('description','');
        $channel    = $dm->getRepository('AppBundle:Channel')->getChannelByName($name);

        if($channel){
            return new Response("The application $name already exists",400);
        } else if(!$name){
            return new Response("Parameter 'name' not found");
        }

        $channel = new Channel();
        $channel->name = $name;
        $channel->description = $description;

        $event = new ChannelCreationEvent($channel);
        $this->get('event_dispatcher')->dispatch(ChannelEvent::ChannelCreation,$event);

        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse($this->get('ar.manager.channel')->channelToArray($channel));
    }

    public function listChannelsAction(){
        $channels = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->findAll();
        return new JsonResponse($this->get('ar.manager.channel')->channelToArray($channels));
    }

    public function deleteChannelAction($cname){
        $channel = $this
                    ->get('doctrine_mongodb')
                    ->getRepository('AppBundle:Channel')
                    ->getChannelByName($cname);

        if(!$channel){
            return new Response("The application $cname was not found",400);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        foreach($channel->scenes as $scene){
            $dm->removeElement($scene);
        }
        foreach($channel->trackables as $trackable){
            $dm->removeElement($trackable);
        }
        $dm->remove($channel);
        $dm->flush();

        return new JsonResponse([]);
    }

    public function getCurrentVersionAction($cname ,Request $request)
    {
        $channel = $this
            ->get('doctrine_mongodb')
            ->getRepository('AppBundle:Channel')
            ->getChannelByName($cname);

        if(!$channel){
            return new Response("The application $cname was not found",400);
        }

        $current = $channel->current;

        if($request->request->get('as',false) == 'json' ){
            return new JsonResponse($this->get('ar.manager.trackable')->trackableToArray($current));
        }

        $filePath = $this->get('ar.manager.scene')->createCurrent($current);

        return true;


    }

}