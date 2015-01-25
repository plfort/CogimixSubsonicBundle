<?php

namespace Cogipix\CogimixSubsonicBundle\ViewHooks\Playlist;

use Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo;
use Cogipix\CogimixCommonBundle\ViewHooks\Playlist\PlaylistRendererInterface;
use Cogipix\CogimixCommonBundle\Utils\LoggerAwareInterface;
use Cogipix\CogimixSubsonicBundle\Services\SubsonicMusicSearch;
/**
 *
 * @author plfort - Cogipix
 *
 */
class PlaylistRenderer implements PlaylistRendererInterface, LoggerAwareInterface {

	private $subsonicServerInfo;
    private $subsonicService;

	public function __construct(SubsonicMusicSearch $subsonicService) {
		$this->subsonicServerInfo = $subsonicService->getSubsonicServerInfo();
		$this->subsonicService = $subsonicService;
	}
	public function getListTemplate() {
		return 'CogimixSubsonicBundle:Playlist:list.html.twig';
	}

	public function getPlaylists($alphaSort = true) {
	    return $this->subsonicService->getPlaylists();
	}

	public function getTag() {
		return 'subsonic';
	}

	public function getRenderPlaylistsParameters()
	{
	    return array(
	        'playlists'=>$this->getPlaylists(),
	        'serverInfo'=>$this->subsonicServerInfo

	    );
	}


	public function setLogger($logger) {
		$this->logger = $logger;
	}
}