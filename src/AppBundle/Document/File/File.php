<?php
/**
 * User: carlos
 * Date: 08/03/2015
 * Time: 15:14
 */

namespace AppBundle\Document\File;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\Version\Version as Version;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @MongoDB\Document
 */
class File
{
    /**
     * @MongoDB\Id()
     */
    public $id;
    /**
     * @MongoDB\Date()
     */
    public $createdAt='';
    /**
     * @MongoDB\String
     */
    private $path='';
    /**
     * @MongoDB\String
     */
    public $name='';
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Version\Version")
     */
    public $version;

    private $file;

    /**
     * Sets file.
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload($dir)
    {

    }

    public function toArray(){
        $retVal = [
            'created_at' => $this->getCreatedAt(),
            'name'       => $this->getName(),
            'id'         => $this->getId()
        ];
        return $retVal;
    }

    public function __construct()
    {
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = time();
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string $path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set version
     *
     * @param \AppBundle\Document\Version\Version $version
     * @return self
     */
    public function setVersion(\AppBundle\Document\Version\Version $version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get version
     *
     * @return \AppBundle\Document\Version\Version $version
     */
    public function getVersion()
    {
        return $this->version;
    }
}
