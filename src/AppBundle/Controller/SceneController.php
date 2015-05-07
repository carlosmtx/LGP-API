<?php

namespace AppBundle\Controller;

use AppBundle\Document\Scene;
use AppBundle\Service\FileSystem\FileType\Compressed\Compressed;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SceneController extends Controller
{
    public function createSceneAction(Request $request,$cname){

        $repos = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel');
        $channel = $repos->getChannelByName($cname);
        /** @var UploadedFile $file */
        $file = $request->files->get('scene');

        $extension = $file->getClientOriginalExtension();

        $dir = $this->container->getParameter('upload_root_dir');

        $newName = time()."_{$file->getClientOriginalName()}";
        $newDir = "$dir/$cname/$newName";

        $this->get('file_system_provider')->mkdir($newDir);

        /** @var Compressed $fileFile */
        $fileFile = $this->get('file_factory')->getByUploadedFile($file);
        if(!is_a($fileFile,Compressed::class)){
            // TODO: Return Error
        }
        $fileFile->extractTo($newDir);

        $scene = new Scene();
        $scene->name = $newName;
        $scene->rootFolder = "$dir/$cname";
        $scene->originalName = $file->getClientOriginalName();

        $channel->scenes->add($scene);
        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($scene);
        $dm->persist($channel);

        $dm->flush();


        return new JsonResponse("ooookk");
    }
}