<?php

namespace AppBundle\Controller;

use AppBundle\Document\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackableController extends Controller
{
    public function createTrackableAction(Request $request,$cname){
        /** @var UploadedFile $file */
        $dm     = $this->get('doctrine_mongodb')->getManager();

        $repos  = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel');
        $channel= $repos->getChannelByName($cname);
        $file = $request->files->get('file',false);

        if($file === false){
            return new Response("Parameter 'file' not found",400);
        }else if(!$channel){
            return new Response("Channel: $channel not found",400);
        }else if(!$request->request->get('name',false)){
            return new Response("Trackable name not specified",400);
        }

        $trackable = new Trackable();
        $trackable->description     = $request->request->get('description',"");
        $trackable->name            = $request->request->get('name',"");
        $trackable->channel         = $channel;

        $trackable->fileOriginalName= $file->getClientOriginalName();

        $dirInfo = $this->get('ar.manager.path')->handleTrackable($trackable);

        $trackable->fileName   = $dirInfo['fileName'];
        $trackable->rootFolder = $dirInfo['rootFolderPath'];


        $this->get('ar.manager.trackable')->copyToDest(
            $file->getRealPath(),
            $dirInfo['rootFolderPath'],
            $dirInfo['fileName']
        );

        $dm->persist($trackable);
        $channel->trackables->add($trackable);

        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse([
            'trackable' => $this->get('ar.manager.trackable')->trackableToArray($trackable)
        ]);
    }

    public function getAction($cname,Request $request)
    {
        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        $trackId = $request->query->get('trackable',false);
        $type    = $request->query->get('as',false);
        if(!$channel){
            return new Response("Channel: $channel not found",400);
        } else if(!$trackId){
            return new Response("Parameter 'trackable' not specified",400);
        }
        $trackable = $this->get('doctrine_mongodb')->getRepository('AppBundle:Trackable')->getTrackableByIdInChannel($trackId,$channel);
        if(!$trackable){
            return new Response("Trackable : $trackId not fount in channel: $cname",400);
        }
        if($request->query->get('as') == 'json'){
            return new JsonResponse(
                $this->get('ar.manager.trackable')->trackableToArray($trackable)
            );
        }

        return new BinaryFileResponse(
            "{$trackable->rootFolder}/{$trackable->fileName}",
            200,
            ['Content-Disposition' => "attachment; filename={$trackable->fileOriginalName};"]
            );
    }

    public function listTrackablesAction($cname){

        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        if(!$channel){
            return new Response("Channel : $cname not found",400);
        }

        $retVal = $this->get('ar.manager.trackable')->trackableToArray($channel->trackables->toArray());
        return new JsonResponse($retVal);
    }

    public function deleteTrackableAction($cname,Request $request){

        $trackId   = $request->query->get('trackable','');
        $channel   = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        $trackable = $this->get('doctrine_mongodb')->getRepository('AppBundle:Trackable')->getTrackableByIdInChannel($trackId,$channel);


        if(!$trackId){
            return new Response("Parameter 'trackable' not found",400);
        } elseif( !$channel ){
            return new Response("Channel $cname not found",400);
        } elseif( !$trackable){
            return new Response("Trackable: $trackId not found in channel : $cname");
        }
        $dm = $this->get('doctrine_mongodb')->getManager();


        foreach($trackable->scenes as $scene){
            $scene->trackables->removeElement($trackable);
            $dm->persist($scene);
        }
        $channel->trackables->removeElement($trackable);
        $dm->remove($trackable);
        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse([]);

    }

    public function linkToAction($cname,Request $request){
        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);

        if(!$channel){
            return new Response("Channel : $cname not found",400);
        }

        if(!$request->request->get('trackable')){
            return new Response("Parameter 'trackable' not found",400);
        } else if(!$request->request->get('scene')){
            return new Response("Parameter 'scene' not found");
        }

        $sceneId = $request->request->get('scene');
        $trackId = $request->request->get('trackable');

        $scene = $this->get('doctrine_mongodb')->getRepository('AppBundle:Scene')->getSceneByIdInChannel($sceneId,$channel);
        $track = $this->get('doctrine_mongodb')->getRepository('AppBundle:Trackable')->getTrackableByIdInChannel($trackId,$channel);

        if(!$scene){
            return new Response("Scene: $scene not found",400);
        }else if(!$track){
            return new Response("Trackable: $track not found",400);
        }

        $scene->trackables->add($track);
        $track->scenes->add($scene);

        $this->get('doctrine_mongodb')->getManager()->persist($scene);
        $this->get('doctrine_mongodb')->getManager()->persist($track);

        $this->get('doctrine_mongodb')->getManager()->flush();

        return new JsonResponse([]);

    }
}