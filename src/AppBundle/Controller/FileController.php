<?php

namespace AppBundle\Controller;

use AppBundle\Document\Channel\Channel;
use AppBundle\Document\File\File;
use AppBundle\Document\Version\Version;
use AppBundle\Event\File\FileCreationEvent;
use AppBundle\Event\File\FileEvent;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    /**
     * Adds file to a version
     * @param $cname
     * @param $vname
     * @param Request $request
     * @return JsonResponse
     */
    public function addFileAction($cname,$vname,Request $request)
    {
        /** @var DocumentRepository $repos */
        /** @var Channel $channel */
        /** @var Version $ver */

        $fileUpload = $request->files->get('file',false);

        if( $fileUpload === false){
            return new Response("Parameter 'file' missing",400);
        }

        $dm       = $this->get('doctrine_mongodb')->getManager();
        $repos    = $dm->getRepository('AppBundle:Channel\Channel');

        $channel = $repos->findOneBy(['name' => $cname]);
        if(!$channel){
            return new Response("Channel: $cname Not Found", 400);
        }


        $version = false;
        foreach($channel->getVersions() as $ver){
            if ($ver->getName() == $vname){
                $version = $ver;
                break;
            }
        }
        if( $version === false) {
            return new Response("Version: $vname Not Found", 400);
        }

        $file = new File();
        $file->setVersion($version);
        $file->setName($cname);

        $event = new FileCreationEvent($file,$fileUpload);
        $this->get('event_dispatcher')->dispatch(FileEvent::FileCreation,$event);

        $version->addFile($file);
        $dm->persist($version);
        $dm->persist($file);
        $dm->flush();


        return new JsonResponse($file->toArray());
    }

    public function getFileAction($cname,$vname,$fname = "",Request $request){
        /** @var DocumentRepository $repos */
        /** @var File $file */
/*        $fileId = $request->query->get('file',false);

        if ( $fileId === false ){
            return new Response('Parameter: \'file\' not found');
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

        return new BinaryFileResponse($filePath);*/

    }
}