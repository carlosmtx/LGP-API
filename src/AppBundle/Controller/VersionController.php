<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel\Channel;
use AppBundle\Document\Version\Version;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $channel->removeVersion($version);

        $dm->remove($version);
        $dm->persist($channel);

        $dm->flush();

        return new JsonResponse($version->toArray());
    }



}