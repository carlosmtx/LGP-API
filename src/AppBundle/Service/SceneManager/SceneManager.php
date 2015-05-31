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
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param FileFactory $fileFactory
     * @param Filesystem $provider
     * @param PathManager $pathManager
     * @param $tmpDir
     */
    public function __construct(FileFactory $fileFactory,Filesystem $provider,PathManager $pathManager,$tmpDir,$twig){
        $this->fileFactory  = $fileFactory;
        $this->tempDir      = $tmpDir;
        $this->provider     = $provider;
        $this->pathManager  = $pathManager;
        $this->twig         = $twig;
    }

    public function __destruct(){
        foreach ( $this->tmpDir as $dirs){
            $this->provider->remove($dirs);
        }
    }
    private function extractToTempDir($path){
        $file = $this->fileFactory->get("$path",FileFactory::ZIP);

        $path = $this->pathManager->getTempDir();
        $this->provider->mkdir($path);
        $file->extractTo($path);

        $this->tmpDir =  $path;
        return $path;
    }


    private function getIndexXMLPath($path){
        if(is_file("$path/index.xml")){
            return "$path/index.xml";
        }else{
            $folder = scandir($path)[0];
            return "$folder/index.xml";
        }
    }





    public function createCurrent(Scene $scene)
    {
        $extractedSceneRoot = $this->extractToTempDir("{$scene->rootFolder}/{$scene->fileName}");
        $indexXMLPath       = $this->getIndexXMLPath($extractedSceneRoot);
        $indexXML           = simplexml_load_file($indexXMLPath);
        $objects            = $indexXML->xpath('/results/object//@id');
        $objectsXML         = $indexXML->xpath('//object');
        $count = 0;
        foreach($scene->trackables as $trackable){
            if($trackable->isDefault){
                $count++;
            }
        }
        $count +=1;
        $i = $count;
        $newObjects = [];
        foreach($scene->trackables as $trackable){
            if($trackable->isDefault) continue;
            foreach($objectsXML as $objectXML){
                $newNode = dom_import_simplexml($objectXML)->cloneNode(true);
                $newNode = simplexml_import_dom($newNode);
                $newNode->assets3d->properties->coordinatesystemid= $i;
                $newNode->attributes()['id'] = $newNode->attributes()['id'] . "_arbankingobject_$i";
                $newObjects[] = $newNode;
            }
            $i++;
        }


        $trackingXMLPath = dirname($indexXMLPath)."/" . $indexXML->xpath('/results//@trackingurl')[0]->trackingurl.'';
        $trackingXMLPathDecompressed = $this->extractToTempDir($trackingXMLPath);
        $trackingXML = simplexml_load_file($trackingXMLPathDecompressed .'/Tracking.xml');
        $i = $count;
        $newSensors = [];
        $newCOS     = [];
        foreach($scene->trackables as $trackable){
            if($trackable->isDefault) continue;
            $sensorCOS  = $this->twig->render('sensor_cos.xml.twig',['index' => $i,'filename' => $trackable->fileOriginalName]);
            $COS        = $this->twig->render('cos.xml.twig',['index' => $i,'filename' => $trackable->fileOriginalName]);
            $newSensors[] = simplexml_load_string($sensorCOS);
            $newCOS[]     = simplexml_load_string($COS);
            $i++;
        }

        $logicJSPath = dirname($indexXMLPath)."/html/arel/js/logic.js";

        $logicJS = file_get_contents($logicJSPath);

        $newJSContent = "var initialContent  = scenario.contents.slice();var avObjects=[";
        foreach($objects as $object){
            $newJSContent .= "'".$object->__toString()."',";
        }
        $newJSContent .= "];";
        $i = $count;
        foreach($scene->trackables as $trackable){
            if($trackable->isDefault ) continue;
            $newJSContent .= $this->twig->render('logic.js.twig',['index'=>$i]);
            $i++;
        }
        $newJSContent .= 'scenario.onStartup();';
        $logicJS = preg_replace('/scenario\.onStartup\(\);/',$newJSContent,$logicJS);


        $indexXML = dom_import_simplexml($indexXML);
        foreach($newObjects as $object){
            $node = dom_import_simplexml($object);
            $indexXML->appendChild($node);
        }


        $trackingXML = dom_import_simplexml($trackingXML);
        foreach($newSensors as $sensor){
            $node = dom_import_simplexml($sensor);
            $node = $trackingXML->ownerDocument->importNode($node,true);
            $trackingXML->getElementsByTagName('Sensor')->item(0)->appendChild($node);
        }
        foreach($newCOS as $cos){
            $node = dom_import_simplexml($cos);
            $node = $trackingXML->ownerDocument->importNode($node,true);
            $trackingXML->getElementsByTagName('Connections')->item(0)->appendChild($node);
        }

        /** @var Trackable $trackable */
        foreach($scene->trackables as $trackable){
            if($trackable->isDefault) continue;
            $path = "{$trackable->rootFolder}/{$trackable->fileName}";
            $this->provider->copy($path,"$trackingXMLPathDecompressed/{$trackable->fileOriginalName}");
        }

        $trackingXML = simplexml_import_dom($trackingXML);
        $indexXML = simplexml_import_dom($indexXML);

        $rootPath = dirname($indexXMLPath);
        $finalLogicJs = fopen("$rootPath/html/arel/js/logic.js",'w+');
        $finalIndexXml= fopen("$rootPath/index.xml",'w+');

        $finalTrackXml= fopen("$trackingXMLPathDecompressed/Tracking.xml",'w+');


        fwrite($finalLogicJs ,$logicJS);fclose($finalLogicJs );
        fwrite($finalIndexXml,$indexXML->saveXML());fclose($finalIndexXml);
        fwrite($finalTrackXml,$trackingXML->saveXML());fclose($finalTrackXml);

        unlink($rootPath .  "/" . $indexXML->xpath('/results//@trackingurl')[0]->trackingurl);
        $zipTrackConfig = $this->fileFactory->get($trackingXMLPathDecompressed ,FileFactory::ZIP);

        $zipTrackConfig->compressTo(FileFactory::ZIP,$rootPath .  "/" . $indexXML->xpath('/results//@trackingurl')[0]->trackingurl);


        $destFolder  = $this->pathManager->getTempDir().'/current.zip';
        $currentFile = $this->fileFactory->get($extractedSceneRoot,FileFactory::ZIP);
        $currentFile->compressTo(FileFactory::ZIP,$destFolder);

        return $destFolder;
    }

    public function getDefaultTrackables(Scene $scene){

        $path = $this->extractToTempDir("{$scene->rootFolder}/{$scene->fileName}");
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