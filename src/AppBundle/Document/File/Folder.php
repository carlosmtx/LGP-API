<?php
/**
 * User: carlos
 * Date: 16/04/2015
 * Time: 16:46
 */

namespace AppBundle\Document\File;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Folder extends File{

    /**
     * @MongoDB\ReferenceMany(targetDocument="File")
     */
    public $children;


    /**
     * @var $id
     */
    public $id;

    /**
     * @var date $createdAt
     */
    public $createdAt;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $extension
     */
    public $extension;

    /**
     * @var \AppBundle\Document\File\Folder
     */
    public $parent;

    /**
     * @var  \AppBundle\Document\Version\Version
     */
    public $version;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add child
     *
     * @param File $child
     */
    public function addChild(File $child)
    {
        $this->children[] = $child;
    }

    /**
     * Remove child
     *
     * @param File $child
     */
    public function removeChild(File $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return $this->children;
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
     * Set extension
     *
     * @param string $extension
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Get extension
     *
     * @return string $extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Document\File\Folder $parent
     * @return self
     */
    public function setParent(\AppBundle\Document\File\Folder $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Document\File\Folder $parent
     */
    public function getParent()
    {
        return $this->parent;
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
