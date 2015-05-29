<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 12/05/2015
 * Time: 10:20
 */

namespace AppBundle\Service\ChannelManager;


use AppBundle\Document\Channel;
use AppBundle\Service\FileSystem\FileFactory;
use AppBundle\Service\PathManager\PathManager;
use Symfony\Component\Filesystem\Filesystem;

class ChannelManager {
    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var Filesystem
     */
    private $provider;
    /**
     * @var PathManager
     */
    private $pathManager;
    private $tmpDir;

    /**
     * @param FileFactory $fileFactory
     * @param Filesystem $provider
     * @param PathManager $pathManager
     * @param $tmpDir
     */
    public function __construct(FileFactory $fileFactory,Filesystem $provider,PathManager $pathManager,$tmpDir){

        $this->fileFactory = $fileFactory;
        $this->provider = $provider;
        $this->pathManager = $pathManager;
        $this->tmpDir = $tmpDir;
    }

    public function channelToArray($channel){
        $single = false;
        $channels = $channel;
        $retVal = [];

        if(is_a($channel,Channel::class)){
            $channels= [$channel];
        }
        /** @var Channel $channel */
        foreach($channels as $channel){
            $retVal[] = [
                'id'         => $channel->id,
                'name'       => $channel->name,
                'description'=> $channel->description,
                'createdAt'  => $channel->createdAt ? $channel->createdAt->format('Y/m/d') : '',
                'updatedAt'  => $channel->updatedAt ? $channel->updatedAt->format('Y/m/d') : '',
                'trackables' => $channel->trackables->count(),
                'scenes'     => $channel->scenes->count()
            ];
        }
        return $single ? $retVal[0] : $retVal;
    }



}