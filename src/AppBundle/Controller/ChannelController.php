<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel\Channel;
use AppBundle\Document\File\File;
use Doctrine\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelController extends Controller
{
    /**
     * Creates a channel
     * @param Request $request
     * @return JsonResponse
     */
    public function createChannelAction(Request $request, $name){
        /** @var \Doctrine\ODM\MongoDB\DocumentRepository $repos */
        if(!$name){
            return new Response('Parameter \'name\' missing' , 400);
        }

        $channel = new Channel();
        $channel->setName($name);
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:Channel\Channel');
        $channel_ = $repos->findOneBy(["name"=> $name]);

        $dm->persist($channel);
        $dm->flush();

        if ( $channel_ ){
            return new JsonResponse($channel->toArray(),200);
        }

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

        $dm->remove($channel);
        $dm->flush();

        return new JsonResponse($channel->toArray());
    }

    public function listChannelFilesAction(Request $request){
        $channelId = $request->query->get('channel',false);

        if($channelId === false){
            $request = json_decode($request->getContent(), true);
            $channelId = $request['channel'];
        }

        if($channelId === false){
            return new Response('Parameter \'channel\' missing',400);
        }
        /** @var DocumentRepository $repos */
        /** @var Channel $channel */
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repos   = $manager->getRepository('AppBundle:Channel\Channel');
        $channel = $repos->findOneBy(['id' => $channelId ]);

        if ( !$channelId ){
            return new Response("Channel Not Found: $channelId",400);
        }
        $channel->getCurrentVersion();
        $current = $channel->getCurrentVersion();

        if ( !$current ){
            return new Response('No Current Version Defined');
        }

        $files_ = $current->getFiles();
        $files  = [];

        /** @var File $file */
        foreach ($files_ ?: [] as $file) {
            $files[] = $file->toArray();
        }

        return new JsonResponse($files);
    }

    public function listChannelVersionsAction(Request $request)
    {
        $channelId = $request->query->get('channel',false);

        if($channelId === false){
            $request = json_decode($request->getContent(), true);
            $channelId = $request['channel'];
        }

        if($channelId === false){
            return new Response('Parameter \'channel\' missing',400);
        }

        /** @var Channel $channel */
        /** @var DocumentRepository $repos */
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repos   = $manager->getRepository('AppBundle:Channel\Channel');
        $channel = $repos->findOneBy(['id' => $channelId ]);
        $channels = [];

        foreach($channel->getVersions()?: [] as $channel){
            $channels[]  = $channel->toArray();
        }

        return new JsonResponse($channels);
    }

}