<?php
/**
 * User: carlos
 * Date: 15/03/2015
 * Time: 19:33
 */

namespace AppBundle\Service\FileSystem;



use AppBundle\Document\Channel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager {
    /** @var FileFactory */
    private $fileFactory;
    private $rootDir;
    private $provider;

    public function __construct(FileFactory $fileFactory,Filesystem $provider, $rootDir){
        $this->rootDir = $rootDir;
        $this->fileFactory = $fileFactory;
        $this->provider = $provider;
    }

    public function createChannel(Channel $channel){
        $this->provider->mkdir("{$this->rootDir}/{$channel->name}");
    }
    /*
    public function removeChannel(Channel $channel){
        foreach($channel->getVersions() as $version){
            $this->removeVersion($version);
        }
        $this->fileFactory->get("{$this->rootDir}/{$channel->getName()}/")->remove();
    }


    public function createVersion(Version $version){
        $this->fileFactory->get(
            "{$this->rootDir}/{$version->getChannel()->getName()}/{$version->getName()}",
            FileFactory::DIR
        )->save();
    }
    public function removeVersion(Version $version){
        foreach($version->getFiles() as $file){
            //$this->removeFile();
        }
        $this->fileFactory->get(
            "{$this->rootDir}/{$version->getChannel()->getName()}/{$version->getName()}",
            FileFactory::DIR
        )->save();
    }

    public function createFile(File $fileDoc,UploadedFile $file){
        $fileFile = $this->fileFactory->getByUploadedFile($file);
        $fileDoc->setExtension($file->getClientOriginalExtension());
        $fileFile->save("{$this->rootDir}/{$fileDoc->getVersion()->getChannel()->getName()}/{$fileDoc->getVersion()->getName()}/");
    }*/


}