<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel\Channel;
use AppBundle\Document\Version\Version;
use AppBundle\Event\Channel\ChannelCreationEvent;
use AppBundle\Event\Channel\ChannelDeleteEvent;
use AppBundle\Event\Channel\ChannelEvent;
use Doctrine\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelController extends Controller
{
    /**
     * Creates a channel
     * @param $name
     * @return JsonResponse
     * @internal param Request $request
     */
    public function createChannelAction($name){
        /** @var \Doctrine\ODM\MongoDB\DocumentRepository $repos */

        $channel = new Channel();
        $channel->setName($name);
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:Channel\Channel');

        $channel_ = $repos->findOneBy(["name"=> $name]);

        if ( $channel_ ){
            return new JsonResponse($channel->toArray(),200);
        }

        $event = new ChannelCreationEvent($channel);
        $this->get('event_dispatcher')->dispatch(ChannelEvent::ChannelCreation,$event);

        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse($channel->toArray(),201);
    }


    /**
     * This Action Returns all the Channels available
     * @return JsonResponse
     */
    public function listChannelsAction()
    {
        /** @var \Doctrine\ODM\MongoDB\DocumentRepository $repos */
        $repos    = $this->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:Channel\Channel');
        /** @var Cursor $channels */
        $channels_ = $repos
                    ->createQueryBuilder()
                    ->getQuery()
                    ->execute();
        $channels = [];
        /** @var Channel $channel */
        foreach($channels_ as $channel){
            $channels[]  = $channel->toArray();
        }

        return new  JsonResponse($channels);
    }

    /**
     * This action deletes one channel from the list
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteChannelAction(Request $request,$name)
    {
        /** @var DocumentRepository $repos */
        $dm       = $this->get('doctrine_mongodb')->getManager();
        $repos    = $dm->getRepository('AppBundle:Channel\Channel');
        $channel  = $repos->findOneBy(['name' => $name]);

        if ( ! $channel ){
            return new Response("Channel: $name doesn't exist",400);
        }

        foreach( $channel->getVersions() as $version ){
            $dm->remove($version);
        }
        $event = new ChannelDeleteEvent($channel);
        $this->get('event_dispatcher')->dispatch(ChannelEvent::ChannelDelete,$event);
        $dm->remove($channel);
        $dm->flush();

        return new JsonResponse($channel->toArray());
    }


}