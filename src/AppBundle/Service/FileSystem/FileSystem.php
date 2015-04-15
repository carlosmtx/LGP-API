<?php
/**
 * User: carlos
 * Date: 15/03/2015
 * Time: 19:33
 */

namespace AppBundle\Service\FileSystem;


use AppBundle\Document\Channel\Channel;
use AppBundle\Document\Version\Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSystem {
    /** @var  \Symfony\Component\Filesystem\Filesystem */
    private $fileSystem;
    private $rootDir;

    public function __construct($fileSystem,$rootDir){
        $this->fileSystem = $fileSystem;
        $this->rootDir = $rootDir;
    }

    public function createChannel(Channel $channel){
        $this->fileSystem->mkdir("{$this->rootDir}/{$channel->getName()}/",0777);
    }
    public function removeChannel(Channel $channel){
        foreach($channel->getVersions() as $version){
            $this->removeVersion($version);
        }
        $this->fileSystem->remove("{$this->rootDir}/{$channel->getName()}/");
    }


    public function createVersion(Version $version){
        $this->fileSystem->mkdir("{$this->rootDir}/{$version->getChannel()->getName()}/{$version->getName()}",0777);
    }
    public function removeVersion(Version $version){
        foreach($version->getFiles() as $file){
            //$this->removeFile();
        }
        $this->fileSystem->remove("{$this->rootDir}/{$version->getChannel()->getName()}/{$version->getName()}",0777);
    }

    public function createFile($cname,$vname,UploadedFile $file){
        $this->getFile()->move(
            $this->rootDir,
            $this->getFile()->getClientOriginalName()
        );
        $this->path = $this->getFile()->getClientOriginalName();
    }


}