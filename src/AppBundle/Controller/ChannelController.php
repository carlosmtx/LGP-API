<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel;
use AppBundle\Event\Channel\ChannelCreationEvent;
use AppBundle\Event\Channel\ChannelEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    public function setCurrentVersionAction($cname,Request $request)
    {
        $dm         = $this->get('doctrine_mongodb')->getManager();

        $sceneId    = $request->request->get('scene',false);
        $description= $request->request->get('description','');
        $channel    = $dm->getRepository('AppBundle:Channel')->getChannelByName($cname);
        $scene      = $dm->getRepository('AppBundle:Scene')->getSceneByIdInChannel($sceneId,$channel);
        if(!$channel){
            return new Response("Channel $cname not found",400);
        } else if(!$sceneId){
            return new Response("Parameter 'scene' not found");
        } else if (!$scene){
            return new Response("Scene : $sceneId not found");
        }

        $channel->current = $scene;
        $scene->preUpdate();
        $dm->persist($channel);
        $dm->persist($scene);

        $dm->flush();

        return new JsonResponse($this->get('ar.manager.channel')->channelToArray($channel));
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
            $dm->remove($scene);

        }
        foreach($channel->trackables as $trackable){
            $dm->remove($trackable);
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

        if(!$current){
            return new Response("The application $cname does not have a default version",400);
        }

        if($request->query->get('as',false) == 'json' ){
            return new JsonResponse($this->get('ar.manager.scene')->sceneToArray($current));
        }
        $current = $this->get('ar.manager.scene')->createCurrent($current);
        return new BinaryFileResponse(
            "$current",
            200,
            ['Content-Disposition' => "attachment; filename=current.zip;"]
        );
    }

}