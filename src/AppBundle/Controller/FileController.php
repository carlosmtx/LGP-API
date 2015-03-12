<?php

namespace AppBundle\Controller;

use AppBundle\Document\File\File;
use AppBundle\Document\Version\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileController extends Controller
{
    /**
     * Adds file to a version
     * @param Request $request
     * @return JsonResponse
     */
    public function addFileAction(Request $request)
    {
        $version    = $request->request->get('version');
        $fileUpload = $request->files->get('file');

        $dm       = $this->get('doctrine_mongodb')->getManager();
        $repos    = $dm->getRepository('AppBundle:Version\Version');

        $file = new File();
        $file->setFile($fileUpload);


        /** @var Version  $version */
        $version  = $repos->findOneBy(['id' => $version]);
        $channel  = $version->getChannel();
        $channel->getName();


        $version->addFile($file);
        $file->setVersion($version);

        $root = $this->container->getParameter('upload_root_dir');
        $channelName = $channel->getName();
        $versionName = $version->getName();
        $dir = $root.'/'.$channelName.'/'.$versionName;

        $file->upload($dir);
        $dm->persist($version);
        $dm->persist($file);

        $dm->flush();

        return new JsonResponse($file);
    }

    public function getFileAction(Request $request){
        $fileId = $request->query->get('file',false);
        if ( $fileId === false ){

        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        $repos = $dm->getRepository('AppBundle:File\File');

        $file = $repos->findOneBy(['id' => $fileId]);

        $root = $this->container->getParameter('upload_root_dir');

        $version = $file->getVersion();
        $channel = $version->getChannel();
        $version = $version->getName();
        $channel = $channel->getName();

        $filePath = $root.'/'.$channel.'/'.$version.'/'.$file->getPath();

        return new BinaryFileResponse($filePath);

    }
}