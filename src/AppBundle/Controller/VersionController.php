<?php

namespace AppBundle\Controller;

use AppBundle\Document\Version\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionController extends Controller
{
    public function createAction(Request $request){
        $name    = $request->request->get('name',false) ;
        $channelId = $request->request->get('channel',false) ;

        if($channelId === false || $name === false){
            $request = json_decode($request->getContent(), true);
            $channelId = $request['channel'];
            $name = $request['name'];
        }



        if ( $channelId === false || $name === false ){
            return new Response('Parameter Missing: name or channel' , 400);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:Channel\Channel');

        $channel = $repos->findOneBy(['id' => $channelId]);

        if ( !$channel ){
            return new Response("Channel Not Found: $channelId");
        }

        $version = new Version();
        $version->setName($name);

        $channel->addVersion($version);
        $version->setChannel($channel);

        $root = $this->container->getParameter('upload_root_dir');
        $channelName = $channel->getName();
        $versionName = $version->getName();
        $fs = new Filesystem();
        $fs->mkdir("$root/$channelName/$versionName",0777);

        $dm->persist($version);
        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse($version->toArray());
    }

    public function deleteAction(Request $request){
        $id = $request->request->get('id',false) ;

        if($id === false ){
            $request = json_decode($request->getContent(), true);
            $id = $request['id'];
        }

        if($id === false){
            return new Response('Parameter id missing',400);
        }



        $dm       = $this->get('doctrine_mongodb')->getManager();
        $repos    = $dm->getRepository('AppBundle:Version\Version');
        /** @var Version  $version */
        $version  = $repos->findOneBy(['id' => $id]);

        if ( !$version ){
            return new Response("Version: $id not found",400);
        }
        $channel = $version->getChannel();
        $channel->removeVersion($version);

        $root = $this->container->getParameter('upload_root_dir');
        $channelName = $channel->getName();
        $versionName = $version->getName();
        $fs = new Filesystem();
        $fs->remove("$root/$channelName/$versionName",0777);

        $dm->remove($version);
        $dm->persist($channel);

        $dm->flush();

        return new JsonResponse($version);
    }


    public function setCurrentAction(Request $request){
        $versionId = $request->request->get('version',false) ;

        if ( $versionId === false){
            return new Response('Parameter: version missing');
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:Version\Version');
        $version = $repos->findOneBy(['id' => $versionId]);

        if ( !$version){
            return new Response("Version Not Found: $versionId");
        }

        $version->getChannel()->setCurrentVersion($version);
        $dm->persist($version->getChannel());
        $dm->flush();
        return new JsonResponse($version->toArray());
    }

}