<?php
namespace Cogipix\CogimixSubsonicBundle\Services;

use Cogipix\CogimixCommonBundle\MusicSearch\AbstractMusicSearch;
use Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo;

class SubsonicMusicSearch extends AbstractMusicSearch
{

    /**
     *
     * @var SubsonicServerInfo $subsonicServerInfo
     */
    private $subsonicServerInfo;

    private $serializer;

    private $resultBuilder;

    private $query_data;

    private $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POST => 1,
        CURLOPT_HTTPHEADER => array(
            'Content-type: application/json'
        ),
        CURLOPT_SSL_VERIFYPEER => false
    );

    private $base_query_data = array(
        'c' => 'cogimix',
        'f' => 'json',
        'v' => '1.4.0'
    );

    public function __construct(ResultBuilder $resultBuilder)
    {
        $this->resultBuilder = $resultBuilder;
    }

    protected function parseResponse($output)
    {
        if (isset($output['subsonic-response']) && isset($output['subsonic-response']['status']) && $output['subsonic-response']['status'] == 'ok') {
            if (isset($output['subsonic-response']['searchResult2'])) {
                if (isset($output['subsonic-response']['searchResult2']['song'])) {
                    return $this->resultBuilder->createArrayFromSubsonicTracks($output['subsonic-response']['searchResult2']['song'], $this->subsonicServerInfo);
                }
            }
        }
        return array();
    }

    /**
     * Ping the current subsonic server
     *
     * @return boolean mixed
     */
    public function testRemote()
    {
        $query_data = $this->base_query_data;
        $this->query_data = http_build_query($query_data);

        $this->CURL_OPTS[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $this->CURL_OPTS[CURLOPT_USERPWD] = $this->subsonicServerInfo->getUsername() . ":" . $this->subsonicServerInfo->getPassword();

        unset($this->CURL_OPTS[CURLOPT_POSTFIELDS]);
        unset($this->CURL_OPTS[CURLOPT_POST]);

        $c = curl_init($this->subsonicServerInfo->getEndPointUrl() . '/rest/ping.view?' . $this->query_data);

        curl_setopt_array($c, $this->CURL_OPTS);
        $output = curl_exec($c);
        if ($output === false) {
            $this->logger->err(curl_error($c));

            return false;
        }
        $response = json_decode($output, true);

        if (! empty($response)) {

            return true;
        }
        return false;
    }

    protected function executeQuery()
    {
        $c = curl_init($this->subsonicServerInfo->getEndPointUrl() . '/rest/search2.view?' . $this->query_data);

        curl_setopt_array($c, $this->CURL_OPTS);

        $output = curl_exec($c);

        if ($output === false) {
            $this->logger->err(curl_error($c));

            return array();
        }

        return $this->parseResponse(json_decode($output, true));
    }

    public function getPlaylistTracks($playlistId)
    {
        $query_data = $this->base_query_data;
        $query_data['id'] = $playlistId;
        $this->CURL_OPTS[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $this->CURL_OPTS[CURLOPT_USERPWD] = $this->subsonicServerInfo->getUsername() . ":" . $this->subsonicServerInfo->getPassword();
        $response = $this->callAPI('getPlaylist.view', $query_data);
        if (! empty($response)) {
            return $this->parsePlaylistTracksResponse($response);
        }
    }

    public function getPlaylists()
    {
        $this->CURL_OPTS[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $this->CURL_OPTS[CURLOPT_USERPWD] = $this->subsonicServerInfo->getUsername() . ":" . $this->subsonicServerInfo->getPassword();

        $response = $this->callAPI('getPlaylists.view', $this->base_query_data);
        // $c = curl_init($this->subsonicServerInfo->getEndPointUrl() . '/rest/getPlaylists.view?'.$this->query_data);

        // curl_setopt_array($c, $this->CURL_OPTS);

        // $output = curl_exec($c);

        // if ($output === false) {
        // $this->logger->err(curl_error($c));

        // return array();
        // }
        // $response = json_decode($output, true);

         $playlists = array ();
        if (! empty($response)) {
            $playlistsArray = $this->parsePlaylistsResponse($response);
            $playlists[$this->subsonicServerInfo->getName()] = $playlistsArray;
        }

        return $playlists;
    }

    protected function parsePlaylistsResponse($output)
    {
        if (isset($output['subsonic-response']) && isset($output['subsonic-response']['status']) && $output['subsonic-response']['status'] == 'ok') {
            if (isset($output['subsonic-response']['playlists']['playlist'])) {
                if (isset($output['subsonic-response']['playlists']['playlist'])) {
                    return $output['subsonic-response']['playlists']['playlist'];
                }
            }
        }
        return array();
    }

    protected function parsePlaylistTracksResponse($output)
    {

        if (isset($output['subsonic-response']) && isset($output['subsonic-response']['status']) && $output['subsonic-response']['status'] == 'ok') {
            if (isset($output['subsonic-response']['playlist'])) {
                if (isset($output['subsonic-response']['playlist']['entry'])) {

                   return  $this->resultBuilder->createArrayFromSubsonicTracks($output['subsonic-response']['playlist']['entry'], $this->subsonicServerInfo);
                }
            }
        }
        return array();
    }

    protected function buildQuery()
    {
        $this->logger->info($this->searchQuery);
        $this->CURL_OPTS[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $this->CURL_OPTS[CURLOPT_USERPWD] = $this->subsonicServerInfo->getUsername() . ":" . $this->subsonicServerInfo->getPassword();
        $query_data = $this->base_query_data;
        $query_data['query'] = $this->searchQuery->getSongQuery();
        $query_data['artistCount'] = 0;
        $query_data['albumCount'] = 0;
        $query_data['songCount'] = 100;
        $this->query_data = http_build_query($query_data);
    }

    private function callAPI($endpoint, $queryData)
    {
        $c = curl_init($this->subsonicServerInfo->getEndPointUrl() . '/rest/' . $endpoint . '?' . http_build_query($queryData));

        curl_setopt_array($c, $this->CURL_OPTS);

        $output = curl_exec($c);

        if ($output !== false) {
            $response = json_decode($output, true);
            if (! empty($response)) {
                return $response;
            }
        } else {
            $this->logger->err(curl_error($c));
        }

        return array();
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