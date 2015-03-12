<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel\Channel;
use Doctrine\MongoDB\Cursor;
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
    public function createAction(Request $request){

        $name = $request->request->get('name',false);

        if(!$name){
            return new Response('Parameter "name" missing' , 400);
        }

        $channel = new Channel();
        $channel->setName($name);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($channel);
        $dm->flush();

        $root = $this->container->getParameter('upload_root_dir');
        $fs = new Filesystem();
        $fs->mkdir("$root/{$channel->getName()}/",0777);

        return new JsonResponse($channel);
    }


    /**
     * This Action Returns all the Channels available
     * @return JsonResponse
     */
    public function listAction()
    {
        /** @var \Doctrine\ODM\MongoDB\DocumentRepository $repos */
        $repos    = $this->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:Channel\Channel');
        /**
         * @var Cursor $channels
         */
        $channels = $repos
                    ->createQueryBuilder()
                    ->hydrate(true)
                    ->select('name')
                    ->getQuery()
                    ->execute();

        return new  JsonResponse($channels->toArray());
    }

    /**
     * This action deletes one channel from the list
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $id = $request->request->get('id') ?: false;

        if(!$id){
            return new Response('Parameter id missing',400);
        }

        $dm       = $this->get('doctrine_mongodb')->getManager();
        $repos    = $dm->getRepository('AppBundle:Channel\Channel');
        $channel  = $repos->findOneBy(['id' => $id]);

        if (!$channel){
            return new Response("Channel: $id doesn't exist",400);
        }

        foreach( $channel->getVersion() as $version ){
            $dm->remove($version);
        }


        $root = $this->container->getParameter('upload_root_dir');
        $fs = new Filesystem();
        $fs->remove("$root/{$channel->getName()}");


        $dm->remove($channel);
        $dm->flush();

        return new JsonResponse($channel);
    }
}