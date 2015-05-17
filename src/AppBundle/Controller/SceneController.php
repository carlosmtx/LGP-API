<?php

namespace AppBundle\Controller;

use AppBundle\Document\Scene;
use AppBundle\Document\Trackable;
use AppBundle\Service\FileSystem\FileType\Compressed\Compressed;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SceneController extends Controller
{
    public function getAction($cname,Request $request)
    {
        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        $sceneId = $request->query->get('scene',false);
        $type    = $request->query->get('as',false);

        if(!$channel){
            return new Response("Channel: $channel not found",400);
        } else if(!$sceneId){
            return new Response("Parameter 'trackable' not specified",400);
        }
        $scene = $this->get('doctrine_mongodb')->getRepository('AppBundle:Scene')->getSceneByIdInChannel($sceneId,$channel);
        if($type == 'json'){
            return new JsonResponse(
                $this->get('ar.manager.scene')->sceneToArray($scene)
            );
        }

        return new BinaryFileResponse(
            "{$scene->rootFolder}/{$scene->fileName}",
            200,
            ['Content-Disposition' => "attachment; filename={$scene->fileOriginalName};"]
        );
    }
    public function listSceneAction($cname){
        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        if(!$channel){
            return new Response("Channel : $cname not found",400);
        }

        $retVal = $this->get('ar.manager.scene')->sceneToArray($channel->scenes->toArray());
        return new JsonResponse($retVal);
    }
    public function createSceneAction(Request $request,$cname){
        /** @var UploadedFile $file */

        $repos   = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel');
        $channel = $repos->getChannelByName($cname);
        $file    = $request->files->get('file',false);

        if($file === false){
            return new Response("Parameter 'file' was not specified",400);
        } else if(!$channel) {
            return new Response("Channel : $cname was not found", 400);
        }

        $scene   = $this->get('ar.manager.scene')->handleRequest($request,$channel);
        $dirInfo = $this->get('ar.manager.path')->handleScene($scene);
        $scene->rootFolder = $dirInfo['rootFolderPath'];
        $scene->fileName   = $dirInfo['fileName'];


        $this->get('ar.manager.scene')->copyToDest(
            $file->getRealPath(),
            $dirInfo['rootFolderPath'],
            $dirInfo['fileName']
        );

        $trackables_ = $this->get('ar.manager.scene')->getDefaultTrackables($scene);
        $trackables  = [];
        foreach($trackables_ as $track){
            $trackable = $this->get('ar.manager.trackable')->handlePath($track['path'],$channel);
            $dirInfo   = $this->get('ar.manager.path')->handleTrackable($trackable);
            $this->get('ar.manager.trackable')->copyToDest(
                $track['path'],
                $dirInfo['rootFolderPath'],
                $dirInfo['fileName']
            );
            $trackable->fileName = $dirInfo['fileName'];
            $trackable->rootFolder=$dirInfo['rootFolderPath'];

            $trackables[] =  $trackable;
        }
        /** @var Trackable $trackable */
        foreach($trackables as $trackable){
            $trackable->scenes->add($scene);
            $scene->trackables->add($trackable);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();

        foreach($trackables as $trackable ){
            $dm->persist($trackable);
        }
        $dm->persist($scene);
        $dm->persist($channel);



        $dm->flush();


        return new JsonResponse([
            'scene'      => $scene,
            'trackables' => $this->get('ar.manager.trackable')->trackableToArray($trackables)
        ]);
    }
}