<?php
/**
 * User: carlos
 * Date: 15/03/2015
 * Time: 19:33
 */

namespace AppBundle\Service\FileSystem;


use AppBundle\Document\Channel\Channel;
use AppBundle\Document\File\File;
use AppBundle\Document\Version\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager {
    /** @var FileFactory */
    private $fileFactory;
    private $rootDir;

    public function __construct(FileFactory $fileFactory, $rootDir){
        $this->rootDir = $rootDir;
        $this->fileFactory = $fileFactory;
    }

    public function createChannel(Channel $channel){
        $this->fileFactory->get(
            "{$this->rootDir}/{$channel->getName()}",
            FileFactory::DIR
        )->save();
    }
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
    }


}