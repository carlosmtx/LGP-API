<?php

namespace AppBundle\Controller;

use AppBundle\Document\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TrackableController extends Controller
{
    public function addTrackableAction(Request $request,$cname){
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $this->get('doctrine_mongodb')->getRepository('AppBundle:Channel');
        $channel = $repos->getChannelByName($cname);


        $files = $request->files->all();
        $trackables = [];


        $dir= "{$this->container->getParameter('upload_root_dir')}/$cname";
        $fs = $this->get('file_system_provider');
        /** @var UploadedFile $file */
        foreach($files as $file){

            $fileName   = time()."_{$cname}_{$file->getClientOriginalName()}";

            $fs->copy($file->getRealPath(),"$dir/$fileName");

            $trackable = new Trackable();
            $trackable->originalName = $file->getClientOriginalName();
            $trackable->fileName = $fileName;
            $trackable->rootFolder = $dir;

            $trackables[] = $trackable;
        }

        foreach($trackables as $trackable){
            $dm->persist($trackable);
            $channel->trackables->add($trackable);
        }

        $dm->persist($channel);
        $dm->flush();

        return new JsonResponse('Okidoki');
    }
}