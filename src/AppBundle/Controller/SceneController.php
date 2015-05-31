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
    public function getAction($cname, Request $request)
    {
        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        $sceneId = $request->query->get('scene', false);
        $type = $request->query->get('as', false);
        $scene = $this->get('doctrine_mongodb')->getRepository('AppBundle:Scene')->getSceneByIdInChannel($sceneId, $channel);

        if (!$channel) {
            return new Response("Channel: $channel not found", 400);
        } else if (!$sceneId) {
            return new Response("Parameter 'scene' not specified", 400);
        } else if (!$scene){
            return new Response("Scene: $sceneId not found", 400);
        }

        if ($type == 'json') {
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

    public function listSceneAction($cname)
    {
        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        if (!$channel) {
            return new Response("Channel : $cname not found", 400);
        }

        $retVal = $this->get('ar.manager.scene')->sceneToArray($channel->scenes->toArray());
        return new JsonResponse($retVal);
    }

    public function createSceneAction(Request $request, $cname)
    {
        /** @var UploadedFile $file */

        $repos       = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel');
        $channel     = $repos->getChannelByName($cname);
        $file        = $request->files->get('file', false);
        $name        = $request->request->get('name', false);
        $description = $request->request->get('description', '');

        if ($file === false) {
            return new Response("Parameter 'file' was not specified", 400);
        } else if (!$channel) {
            return new Response("Channel : $cname was not found", 400);
        } else if (!$name) {
            return new Response("Parameter 'name' was not found", 400);
        }

        $scene = $this->get('ar.manager.scene')->handleRequest($request, $channel);

        $dirInfo = $this->get('ar.manager.path')->handleScene($scene);
        $scene->name = $name;
        $scene->description = $description;
        $scene->rootFolder = $dirInfo['rootFolderPath'];
        $scene->fileName = $dirInfo['fileName'];


        $this->get('ar.manager.scene')->copyToDest(
            $file->getRealPath(),
            $dirInfo['rootFolderPath'],
            $dirInfo['fileName']
        );

        $trackables_ = $this->get('ar.manager.scene')->getDefaultTrackables($scene);
        $trackables = [];
        foreach ($trackables_ as $track) {
            $trackable = $this->get('ar.manager.trackable')->handlePath($track['path'], $channel);
            $dirInfo = $this->get('ar.manager.path')->handleTrackable($trackable);
            $this->get('ar.manager.trackable')->copyToDest(
                $track['path'],
                $dirInfo['rootFolderPath'],
                $dirInfo['fileName']
            );
            $trackable->fileName = $dirInfo['fileName'];
            $trackable->rootFolder = $dirInfo['rootFolderPath'];

            $trackables[] = $trackable;
        }
        $dm = $this->get('doctrine_mongodb')->getManager();

        /** @var Trackable $trackable */
        foreach ($trackables as $key => $trackable) {
            $trackable->scenes->add($scene);
            $scene->trackables->add($trackable);
            $trackable->description = "Trackable provided by scene {$scene->name}";
            $trackable->name = "{$scene->name}_$key";
            $trackable->isDefault = true;
        }


        foreach ($trackables as $trackable) {
            $dm->persist($trackable);
        }
        $dm->persist($scene);
        $dm->persist($channel);

        $dm->flush();

        return new JsonResponse([
            'scene' => $this->get('ar.manager.scene')->sceneToArray($scene),
            'trackables' => $this->get('ar.manager.trackable')->trackableToArray($trackables)
        ]);
    }


    public function deleteSceneAction(Request $request,$cname){

        $channel = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel')->getChannelByName($cname);
        $scene   = $request->query->get('scene',false);
        $sceneO  = $this->get('doctrine_mongodb')->getRepository('AppBundle:Scene')->getSceneByIdInChannel($scene,$channel);

        if(!$channel){
            return new Response("Channel: $cname was not found",400);
        }else if(!$scene){
            return new Response("Parameter 'scene' was not found",400);
        }else if(!$sceneO){
            return new Response("Scene: $scene was not found",400);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        /** @var Trackable $trackable */
        foreach($sceneO->trackables as $trackable){
            $trackable->scenes->removeElement($sceneO);
            $dm->persist($trackable);
        }
        $channel->scenes->removeElement($sceneO);
        $dm->remove($sceneO);
        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse([]);

    }
}