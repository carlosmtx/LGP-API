<?php
/**
 * User: carlos
 * Date: 02/05/2015
 * Time: 16:47
 */
namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\ChannelRepository")
 */
class Channel extends Document{

    /** @MongoDB\String() */
    public $name;
    /** @MongoDB\String() */
    public $description;
    /** @MongoDB\ReferenceMany(targetDocument="Scene")
     *  @var ArrayCollection  $scenes */
    public $scenes ;
    /** @MongoDB\ReferenceMany(targetDocument="Trackable")
     *  @var ArrayCollection  $trackables */
    public $trackables;

    public function __construct(){
        parent::__construct();
        $this->trackables = new ArrayCollection();
        $this->scenes = new ArrayCollection();
    }
}