<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 10/05/2015
 * Time: 15:43
 */

namespace AppBundle\Service\PathManager;


use AppBundle\Document\Channel;
use AppBundle\Document\Scene;
use AppBundle\Document\Trackable;
use Symfony\Component\Filesystem\Filesystem;

class PathManager {
    const SCENE = 'scene';
    const TRACKABLE = 'trackable';
    private $permanentDir;
    private $tempDir;
    /** @var $fsProvider Filesystem  */
    private $fsProvider;
    private $counter = 0;
    private function generateUniqueRandom(){
        return $this->counter++;
    }
    public function __construct($permanentDir,$tempDir, $fsProvider){
        $this->permanentDir = $permanentDir;
        $this->tempDir = $tempDir;
        $this->fsProvider = $fsProvider;

    }

    public function getChannelDir(Channel $channel){
        $dir = "{$this->permanentDir}/{$channel->id}";
        return $dir;
    }

    public function getSceneDir(Channel $channel,Scene $scene){

    }

    public function getTrackablePath(Trackable $trackable){
        $channelDir = $this->getChannelDir($trackable->channel);
        return  "$channelDir/".PathManager::SCENE."";
    }

    public function handleScene(Scene $scene){
        $channelDir = $this->getChannelDir($scene->channel);
        return [
            'fileName'         => time()."_{$this->generateUniqueRandom()}",
            'rootFolderPath'   => "$channelDir/".PathManager::SCENE."",
            'fullPath'         => "$channelDir/".PathManager::SCENE."/".time()
        ];
    }

    public function handleTrackable(Trackable $trackable){
        $channelDir = $this->getChannelDir($trackable->channel);
        return [
            'fileName'         => time()."_{$this->generateUniqueRandom()}",
            'rootFolderPath'   => "$channelDir/".PathManager::TRACKABLE."",
            'fullPath'         => "$channelDir/".PathManager::TRACKABLE."/".time()
        ];
    }

    public function getTempDir(){
        $dir = "{$this->tempDir}/{$this->generateUniqueRandom()}".time();
        $this->fsProvider->mkdir($dir);
        return $dir;
    }

}