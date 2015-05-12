<?php
namespace Cogipix\CogimixSubsonicBundle\Services;

use Cogipix\CogimixCommonBundle\ResultBuilder\ResultBuilderInterface;
use Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo;
use Cogipix\CogimixSubsonicBundle\Entity\SubsonicResult;
class ResultBuilder implements ResultBuilderInterface{

    private $defaultThumbnails = '/bundles/cogimixsubsonic/images/subsonic.png';

    public function createFromSubsonicTrack($subsonicTrack,SubsonicServerInfo $subsonicServerInfo)
    {
        $item =null;

        if(!empty($subsonicTrack)){
            $item = new SubsonicResult();

            $item->setEntryId($subsonicServerInfo->getAlias().'_'.$subsonicTrack['id']);
            if(isset($subsonicTrack['artist'])){
                $item->setArtist($subsonicTrack['artist']);
            }
            if(isset($subsonicTrack['title'])){
                $item->setTitle($subsonicTrack['title']);
            }

            $item->setUrl($this->buildStreamUrl($subsonicTrack['id'], $subsonicServerInfo));
            if(isset($subsonicTrack['coverArt'])){
                $item->setThumbnails($this->buildThumbnailUrl($subsonicServerInfo,$subsonicTrack['coverArt']));

            }else{
                $item->setThumbnails($this->defaultThumbnails);
            }

            $item->setTag($this->getResultTag());
            $item->setIcon($this->getDefaultIcon());
            $item->setDuration($subsonicTrack['duration']);

        }
        return $item;
    }

    public function createArrayFromSubsonicTracks($subsonicTracks,SubsonicServerInfo $subsonicServerInfo)
    {
        $tracks =array();
        if(!empty($subsonicTracks)){
            foreach($subsonicTracks as $subsonicTrack){
                $item = $this->createFromSubsonicTrack($subsonicTrack,$subsonicServerInfo);
                if($item !==null){
                    $tracks[]=$item;
                }
            }
        }
        return $tracks;
    }

    private function buildThumbnailUrl(SubsonicServerInfo $subsonicServerInfo,$coverId)
    {
        $queryData = array();
        $queryData['id']=$coverId;
        $queryData['size']=200;
        $queryData['u']=$subsonicServerInfo->getUsername();
        $queryData['p']=$subsonicServerInfo->getPassword();

        $queryData['f']='json';

        $queryData['c']= 'cogimix';
        $queryData['v']='1.2.0';

        return rtrim($subsonicServerInfo->getEndPointUrl(),'/').'/rest/getCoverArt.view?'. http_build_query($queryData);

    }

    /**
     * @TODO
     * @param unknown $songId
     * @param SubsonicServerInfo $subsonicServerInfo
     * @return string
     */
    private function buildStreamUrl($songId,SubsonicServerInfo $subsonicServerInfo)
    {
        $queryData = array();
        $queryData['id']=$songId;
        $queryData['u']=$subsonicServerInfo->getUsername();
        $queryData['p']=$subsonicServerInfo->getPassword();
        $queryData['c']= 'cogimix';
        $queryData['f']='json';
        $queryData['v']='1.2.0';
        $queryData['estimateContentLength']=true;
        return rtrim($subsonicServerInfo->getEndPointUrl(),'/').'/rest/stream.view?'. http_build_query($queryData);

    }

    public function getResultTag(){
        return 'stream';
    }

    public function getDefaultIcon(){
        return '/bundles/cogimixsubsonic/images/subsonic-icon.png';
    }

}