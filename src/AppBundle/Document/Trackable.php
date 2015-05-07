<?php
/**
 * User: carlos
 * Date: 02/05/2015
 * Time: 15:33
 */
namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/** @MongoDB\Document(repositoryClass="AppBundle\Repository\TrackableRepository") */
class Trackable extends Document{
    /** @MongoDB\String() */
    public $rootFolder;
    /** @MongoDB\String() */
    public $fileName;
    /** @MongoDB\String() */
    public $originalName;
    /**@MongoDB\String() */
    public $description;
    /**@MongoDB\ReferenceMany(targetDocument="Scene")
     *  @var ArrayCollection  $scenes*/
    public $scenes;
    /** @MongoDB\ReferenceOne(targetDocument="Channel") */
    public $channel;

    public function __construct(){
        parent::__construct();
        $this->scenes = new ArrayCollection();
    }


}