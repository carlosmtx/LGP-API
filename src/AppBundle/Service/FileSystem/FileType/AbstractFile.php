<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 15/04/2015
 * Time: 18:12
 */

namespace AppBundle\Service\FileSystem\FileType;


abstract class AbstractFile {

    protected $rootDir;
    protected $type;

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    abstract function save();


}