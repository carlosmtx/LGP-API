<?php
/**
 * User: carlos
 * Date: 02/05/2015
 * Time: 15:48
 */
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\MappedSuperclass()
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 */
class Document {
    /** @MongoDB\Id() */
    public $id;
    /** @MongoDB\Timestamp() */
    public $createdAt;
    /** @MongoDB\Timestamp() */
    public $updatedAt;
    /** @MongoDB\PreUpdate()*/
    public function preUpdate(){
        $this->updatedAt = time();
    }
    public function __construct(){
        $this->createdAt = time();
    }
}