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
    /** @MongoDB\String() */
    public $fileName;
    /** @MongoDB\String() */
    public $fileOriginalName;
    /** @MongoDB\String() */
    public $description;
    /** @MongoDB\ReferenceMany(targetDocument="Trackable")
     *  @var ArrayCollection  $trackables */
    public $trackables;
    /**
     * @MongoDB\ReferenceOne(targetDocument="Channel")
     * @var Channel $channel
     */
    public $channel;


    public function __construct(){
        parent::__construct();
        $this->trackables = new ArrayCollection();
    }

}
