<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel\Channel;
use AppBundle\Document\Version\Version;
use AppBundle\Event\Version\VersionCreationEvent;
use AppBundle\Event\Version\VersionDeleteEvent;
use AppBundle\Event\Version\VersionEvent;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionController extends Controller
{

    public function createVersionAction($cname,$vname){
        /** @var DocumentRepository  $repos */
        /** @var Channel $channel */

        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:Channel\Channel');

        $channel = $repos->findOneBy(['name' => $cname]);

        if ( !$channel ){
            return new Response("Channel Not Found: $cname");
        }

        $version = new Version();
        $version->setName($vname);

        $channel->addVersion($version);
        $version->setChannel($channel);

        $event = new VersionCreationEvent($version);
        $this->get('event_dispatcher')->dispatch(VersionEvent::VersionCreation,$event);

        $dm->persist($version);
        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse($version->toArray());
    }

    public function deleteAction($cname,$vname){
        /** @var DocumentRepository  $repos */

        $dm       = $this->get('doctrine_mongodb')->getManager();
        $repos    = $dm->getRepository('AppBundle:Version\Version');
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
        /** @var Version  $version */
        $version = $result->toArray(false)[0];

        $channel = $version->getChannel();
        if ($channel->getCurrentVersion() === $version){
            return new Response("Current Version can't be deleted",409);
        }
        $event = new VersionDeleteEvent($version);
        $this->get('event_dispatcher')->dispatch(VersionEvent::VersionDelete,$event);
        $channel->removeVersion($version);

        $dm->remove($version);
        $dm->persist($channel);

        $dm->flush();

        return new JsonResponse($version->toArray());
    }

    public function getCurrentVersionAction($cname,Request $request){

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

        $type = $request->query->get('as',false);
        if($type != false){
            $file = $this->get('file_factory')->get(
                "{$this->container->getParameter('upload_root_dir')}/$cname/{$current->getName()}",
                $request->query->get('as')
            );
            return new BinaryFileResponse($file->toFile());
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

    public function listChannelVersionsAction($cname)
    {

        /** @var Channel $channel */
        /** @var DocumentRepository $repos */
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repos   = $manager->getRepository('AppBundle:Channel\Channel');
        $channel = $repos->findOneBy(['name' => $cname ]);
        if(!$channel){
            return new Response("Channel Not Found: $cname",400);
        }

        $channels = [];

        foreach($channel->getVersions()?: [] as $channel){
            $channels[]  = $channel->toArray();
        }

        return new JsonResponse($channels);
    }


}