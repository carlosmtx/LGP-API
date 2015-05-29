<?php
/**
 * User: carlos
 * Date: 02/05/2015
 * Time: 15:48
 */
namespace AppBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\MappedSuperclass()
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 */
class Document {
    /**
     * @MongoDB\Id()
     */
    public $id;
    /**
     * @MongoDB\Date()
     * @var DateTime
     */
    public $createdAt;
    /**
     * @MongoDB\Date()
     * @var DateTime
     */
    public $updatedAt;
    /**
     * @MongoDB\PreUpdate()
     */
    public function preUpdate(){
        $this->updatedAt = new DateTime();
    }
    public function __construct(){
        $this->createdAt = new DateTime();
    }
}
