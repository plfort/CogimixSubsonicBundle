<?php
namespace Cogipix\CogimixSubsonicBundle\Services;


use Cogipix\CogimixCommonBundle\MusicSearch\AbstractMusicSearch;
use Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo;

class SubsonicMusicSearch extends AbstractMusicSearch
{

    /**
     *
     * @var CustomProviderInfo $customProviderInfo
     */
    private $subsonicServerInfo;

    private $serializer;
    
    private $resultBuilder;
    
    private $query_data;

    private $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POST => 1,
        CURLOPT_HTTPHEADER => array(
            'Content-type: application/json'
        ),
        CURLOPT_SSL_VERIFYPEER => false
    );
    
    public function __construct(ResultBuilder $resultBuilder){
        $this->resultBuilder = $resultBuilder;
    }

    protected function parseResponse($output)
    {
      
        if(isset($output['subsonic-response']) && isset($output['subsonic-response']['status']) && $output['subsonic-response']['status'] == 'ok'){
            if(isset($output['subsonic-response']['searchResult2'])){
                if(isset($output['subsonic-response']['searchResult2']['song'])){
                   return $this->resultBuilder->createArrayFromSubsonicTracks($output['subsonic-response']['searchResult2']['song'], $this->subsonicServerInfo);
                }
            }
        }
        return array();
    }

    /**
     * Ping the current provider
     * 
     * @return boolean mixed
     */
    public function testRemote()
    {
        $this->buildQuery();
        unset($this->CURL_OPTS[CURLOPT_POSTFIELDS]);
        unset($this->CURL_OPTS[CURLOPT_POST]);
        $c = curl_init($this->subsonicServerInfo->getEndPointUrl() . '/ping');
        
        curl_setopt_array($c, $this->CURL_OPTS);
        $output = curl_exec($c);
        if ($output === false) {
            $this->logger->err(curl_error($c));
            
            return false;
        }
        $response = json_decode($output, true);
        
        if (isset($response['count'])) {
            
            return $response;
        }
        return false;
    }

    protected function executeQuery()
    {
        $c = curl_init($this->subsonicServerInfo->getEndPointUrl() . '/rest/search2.view?'.$this->query_data);
       
        curl_setopt_array($c, $this->CURL_OPTS);
        
        $output = curl_exec($c);
        
        if ($output === false) {
            $this->logger->err(curl_error($c));
            
            return array();
        }
        
        return $this->parseResponse(json_decode($output,true));
    }

    protected function buildQuery()
    {
        $this->logger->info($this->searchQuery);
        $this->CURL_OPTS[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $this->CURL_OPTS[CURLOPT_USERPWD] = $this->subsonicServerInfo->getUsername() . ":" . $this->subsonicServerInfo->getPassword();
        $query_data = array();
 
        $query_data['c']='cogimix';
        $query_data['f']='json';
        $query_data['v']='1.9.0';
        $query_data['query']=$this->searchQuery->getSongQuery();
        $this->query_data = http_build_query($query_data);
    }

    public function getName()
    {
        return $this->subsonicServerInfo->getName();
    }

    public function getAlias()
    {
        return $this->subsonicServerInfo->getAlias();
    }

    public function getResultTag()
    {
        return 'stream';
    }

    public function getDefaultIcon()
    {
        return '/bundles/cogimixsubsonic/images/subsonic.png';
    }

    public function getSubsonicServerInfo()
    {
        return $this->subsonicServerInfo;
    }

    public function setSubsonicServerInfo(SubsonicServerInfo $subsonicServerInfo)
    {
        $this->subsonicServerInfo = $subsonicServerInfo;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }
}

?>