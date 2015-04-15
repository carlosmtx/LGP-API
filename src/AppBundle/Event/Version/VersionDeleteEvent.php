<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 16:07
 */

namespace AppBundle\Event\Version;


use AppBundle\Document\Version\Version;
use Symfony\Component\EventDispatcher\Event;

class VersionDeleteEvent extends Event{

    /**
     * @var Version
     */
    private $version;

    public function __construct(Version $version){

        $this->version = $version;

    }


    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }

}