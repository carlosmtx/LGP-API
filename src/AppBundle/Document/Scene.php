<?php
/**
 * User: carlos
 * Date: 02/05/2015
 * Time: 15:33
 */
namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


/** @MongoDB\Document(repositoryClass="AppBundle\Repository\SceneRepository") */
class Scene extends Document{
    /** @MongoDB\String() */
    public $name;
    /** @MongoDB\String() */
    public $rootFolder;
    /**@MongoDB\String() */
    public $originalName;
    /** @MongoDB\String() */
    public $confFilePath;
    /**@MongoDB\String() */
    public $description;
    /**@MongoDB\ReferenceMany(targetDocument="Scene")
     *  @var ArrayCollection  $trackables */
    public $trackables;
    /** @MongoDB\ReferenceOne(targetDocument="Channel") */
    public $channel;


    public function __construct(){
        parent::__construct();
        $this->trackables = new ArrayCollection();
    }
}