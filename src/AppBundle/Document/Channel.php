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
    /** @MongoDB\ReferenceMany(targetDocument="Scene" , cascade={"remove", "persist"})
     *  @var ArrayCollection  $scenes */
    public $scenes ;
    /** @MongoDB\ReferenceMany(targetDocument="Trackable", cascade={"remove", "persist"})
     *  @var ArrayCollection  $trackables */
    public $trackables;
    /** @MongoDB\ReferenceOne(targetDocument="Scene" , cascade={"remove", "persist"})
     *  @var Scene  $current */
    public $current;

    public function __construct(){
        parent::__construct();
        $this->trackables = new ArrayCollection();
        $this->scenes = new ArrayCollection();
    }

}
