<?php
/**
 * User: carlos
 * Date: 10/05/2015
 * Time: 15:31
 */

namespace AppBundle\Service\TrackableManager;


use AppBundle\Document\Channel;
use AppBundle\Document\Scene;
use AppBundle\Document\Trackable;
use AppBundle\Service\FileSystem\FileFactory;
use AppBundle\Service\PathManager\PathManager;
use Symfony\Component\Filesystem\Filesystem;

class TrackableManager
{

    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var Filesystem
     */
    private $provider;
    /**
     * @var PathManager
     */
    private $pathManager;
    private $tmpDir;

    public function __construct(FileFactory $fileFactory, Filesystem $provider, PathManager $pathManager, $tmpDir)
    {

        $this->fileFactory = $fileFactory;
        $this->provider = $provider;
        $this->pathManager = $pathManager;
        $this->tmpDir = $tmpDir;
    }

    public function trackableToArray($trackable){
        $single = false;
        if(is_a($trackable,Trackable::class)){
            $single = true;
            $trackables = [$trackable];
        }else{
            $trackables = $trackable;
        }

        $retVal = [];
        /** @var Trackable $trackable */
        foreach($trackables ?: [] as $trackable){
            $scenes = [];
            /** @var Scene $scene */
            foreach($trackable->scenes ?: [] as $scene){
                $scenes[] = [
                    'id'   => $scene->id,
                    'name' => $scene->name
                ];
            }
            $retVal[] = [
                'id'          => $trackable->id,
                'channel'     => $trackable->channel->name,
                'name'        => $trackable->name,
                'description' => $trackable->description,
                'scenes'      => $scenes,
                'fileName'    => $trackable->fileOriginalName,
                'createdAt'   => $trackable->createdAt,
                'updatedAt'   => $trackable->updatedAt
            ];
        }
        return $single ? $retVal[0] : $retVal;
    }

    public function buildTrackableObject($values)
    {

    }

    public function addToScene()
    {

    }

    public function handlePath($path,Channel $channel)
    {
        $trackable = new Trackable();
        $trackable->fileOriginalName = basename($path);
        $trackable->channel = $channel;
        $channel->trackables->add($trackable);
        return $trackable;
    }

    public function copyToDest($src, $dest, $name)
    {
        $this->provider->mkdir($dest);
        $this->provider->copy($src, "$dest/$name");
    }
}