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

    public function listCurrentVersionAction($cname){

        /** @var DocumentRepository $repos */
        /** @var Channel $channel */
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repos   = $manager->getRepository('AppBundle:Channel\Channel');
        $channel = $repos->findOneBy(['name' => $cname ]);

        if ( !$channel ){
            return new Response("Channel Not Found: $cname",400);
        }

        $channel->getCurrentVersion();
        $current = $channel->getCurrentVersion();

        if ( !$current ){
            return new Response('No Current Version Defined');
        }

        return new JsonResponse($current->toArray());
    }

    public function setCurrentVersionAction($vname, $cname)
    {
        /** @var DocumentRepository  $repos */
        /** @var Version  $version */

        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:Version\Version');

        $qb = $repos->createQueryBuilder();
        $result  = $qb
            ->limit(1)
            ->addAnd(
                $qb->expr()->equals(['name' => $vname]),
                $qb->expr()->equals(['channel.name' => $cname])
            )->getQuery()->execute();

        if($result->count() === 0){
            return new Response("Version: $vname on Channel: $cname not found",400);
        }

        $version = $result->toArray(false)[0];

        $version->getChannel()->setCurrentVersion($version);

        $dm->persist($version->getChannel());
        $dm->flush();

        return new JsonResponse($version->toArray());
    }

    public function listChannelVersionsAction($name)
    {

        /** @var Channel $channel */
        /** @var DocumentRepository $repos */
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repos   = $manager->getRepository('AppBundle:Channel\Channel');
        $channel = $repos->findOneBy(['name' => $name ]);
        if(!$channel){
            return new Response("Channel Not Found: $name",400);
        }

        $channels = [];

        foreach($channel->getVersions()?: [] as $channel){
            $channels[]  = $channel->toArray();
        }

        return new JsonResponse($channels);
    }

}