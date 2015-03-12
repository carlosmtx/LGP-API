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
    protected $id;
    /**
     * @MongoDB\String()
     */
    protected $name;
    /**
     * @MongoDB\ReferenceMany(targetDocument="AppBundle\Document\Version\Version")
     */
    protected $versions;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Version\Version" , nullable=true)
     */
    protected $currentVersion;

    public function toArray(){
        /**
         * @var $ver Version
         */
        $versions = [];
        foreach( $this->getVersions() ?: [] as $ver) {
            $versions[] = [
                'name' => $ver->getName(),
                'id'   => $ver->getId()
            ];
        }
        $retVal = [
                'id'            => $this->getId(),
                'name'          => $this->getName(),
                'versions'      => $versions,

        ];
        if ( $this->getCurrentVersion() ){
            $retVal['currentVersion'] = [
                'name' => $this->getCurrentVersion()->getName(),
                'id'   => $this->getCurrentVersion()->getId()
            ];
        }
        return $retVal;
    }


    public function __construct()
    {
        $this->version = new \Doctrine\Common\Collections\ArrayCollection();
        $this->currentVersion = null;
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
        $this->versions[] = $version;
    }

    /**
     * Remove version
     *
     * @param \AppBundle\Document\Version\Version $version
     */
    public function removeVersion(\AppBundle\Document\Version\Version $version)
    {
        $this->versions->removeElement($version);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection $versions
     */
    public function getVersions()
    {
        return $this->versions;
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
}
