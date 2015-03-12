<?php
/**
 * User: carlos
 * Date: 08/03/2015
 * Time: 16:12
 */

namespace AppBundle\Document\Channel;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\Version\Version as Version;
/**
 * @MongoDB\Document
 */
class Channel {
    /**
     * @MongoDB\Id()
     */
    public $id;
    /**
     * @MongoDB\String()
     */
    public $name;
    /**
     * @MongoDB\ReferenceMany(targetDocument="AppBundle\Document\Version\Version")
     */
    public $version;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Version\Version" , nullable=true)
     */
    public $currentVersion;



    public function __construct()
    {
        $this->version = new \Doctrine\Common\Collections\ArrayCollection();
        $this->currentVersion = null;
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
     * Add version
     *
     * @param \AppBundle\Document\Version\Version $version
     */
    public function addVersion(\AppBundle\Document\Version\Version $version)
    {
        $this->version[] = $version;
    }

    /**
     * Remove version
     *
     * @param \AppBundle\Document\Version\Version $version
     */
    public function removeVersion(\AppBundle\Document\Version\Version $version)
    {
        $this->version->removeElement($version);
    }

    /**
     * Get version
     *
     * @return \Doctrine\Common\Collections\Collection $version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set currentVersion
     *
     * @param \AppBundle\Document\Version\Version $currentVersion
     * @return self
     */
    public function setCurrentVersion(\AppBundle\Document\Version\Version $currentVersion)
    {
        $this->currentVersion = $currentVersion;
        return $this;
    }

    /**
     * Get currentVersion
     *
     * @return \AppBundle\Document\Version\Version $currentVersion
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
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


}
