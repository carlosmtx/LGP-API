<?php
/**
 * User: carlos
 * Date: 07/05/2015
 * Time: 17:24
 */
namespace AppBundle\Service\SceneManager;


use AppBundle\Document\Channel;
use AppBundle\Document\Scene;
use AppBundle\Document\Trackable;
use AppBundle\Service\FileSystem\FileFactory;
use AppBundle\Service\PathManager\PathManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class SceneManager {
    private $fileFactory;
    private $tmpDir = [];
    private $tempDir;
    /**
     * @var Filesystem
     */
    private $provider;
    /**
     * @var PathManager
     */
    private $pathManager;

    /**
     * @param FileFactory $fileFactory
     * @param Filesystem $provider
     * @param PathManager $pathManager
     * @param $tmpDir
     */
    public function __construct(FileFactory $fileFactory,Filesystem $provider,PathManager $pathManager,$tmpDir){
        $this->fileFactory  = $fileFactory;
        $this->tempDir      = $tmpDir;
        $this->provider     = $provider;
        $this->pathManager  = $pathManager;
    }

    public function __destruct(){
        foreach ( $this->tmpDir as $dirs){
            $this->provider->remove($dirs);
        }
    }
    private function extractToTempDir(Scene $scene){
        $file = $this->fileFactory->get("{$scene->rootFolder}/{$scene->fileName}",FileFactory::ZIP);

        $path = "{$this->tempDir}/_".time();
        $this->provider->mkdir($path);
        $file->extractTo($path);

        $this->tmpDir =  $path;
        return $path;
    }

    private function getTrackablesXMLPath($rootPath){
        $xml = simplexml_load_file("$rootPath/index.xml");
    }





    public function createCurrent(Scene $current)
    {
        $dir = $this->getDefaultTrackables($current);

        $trackables = $current->trackables;

        $result = glob("$dir/*.xml");




        return $dir;
    }

    public function getDefaultTrackables(Scene $scene){

        $path = $this->extractToTempDir($scene);
        if(!is_dir("$path/html/resources")){
            //TODO: Throw   error
        }
        $result = glob("$path/html/resources/TrackingConfig*.zip");
        if(!$result[0]){
            //TODO: Error checking and handling
        }
        $trackables = [];
        $trackablesConfig = $this->fileFactory->getByPath($result[0]);
        $tmpDir = "{$this->tempDir}/trackables/".time();
        $this->provider->mkdir("{$this->tempDir}/trackables/".time());
        $trackablesConfig->extractTo($tmpDir);

        $result = glob("$tmpDir/*.xml");

        $xmlObj = simplexml_load_file($result[0]);
        foreach($xmlObj->Sensors->Sensor->SensorCOS as $trackable ){
            $trackables[] = [
                'path'  => $tmpDir.'/'.$trackable->Parameters->ReferenceImage->__toString(),
                'name'  => $trackable->Parameters->ReferenceImage->__toString()
            ];
        }

        return $trackables;
    }
    public function handleRequest(Request $request,Channel $channel){
        $scene = new Scene();
        $scene->fileOriginalName = $request->files->get('file')->getClientOriginalName();
        $scene->channel  = $channel;
        $channel->scenes->add($scene);

        return $scene;
    }
    public function createTrackablesByPath($path){
        return [];
    }

    public function copyToDest($src,$dest,$name){
        $this->provider->mkdir($dest);
        $this->provider->copy($src,"$dest/$name");
    }

    public function sceneToArray($scene)
    {
        $single = false;
        if(is_a($scene,Scene::class)){
            $single = true;
            $scenes = [$scene];
        }else{
            $scenes = $scene;
        }

        $retVal = [];

        /** @var Scene $scene */
        foreach($scenes as $scene){
            $trackables = [];
            /** @var Trackable $trackable */
            foreach($scene->trackables as $trackable){
                $trackables[] = [
                    'id'  => $trackable->id,
                    'name'=> $trackable->name
                ];
            }
            $retVal[] =[
                'id' => $scene->id,
                'name' => $scene->name,
                'description'=> $scene->description,
                'createdAt'  => $scene->createdAt ? $scene->createdAt->format('Y/m/d') : '',
                'updatedAt'  => $scene->updatedAt ? $scene->updatedAt->format('Y/m/d H:m:s') : '',
                'trackables' => $trackables,
                'current'    => $scene->channel->current ?  $scene->channel->current->id == $scene->id : false
            ];
        }
        return $retVal;
    }
}