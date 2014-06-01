<?php
namespace Cogipix\CogimixSubsonicBundle\Entity;

use Cogipix\CogimixCommonBundle\Entity\TrackResult;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;
/**
  * @JMSSerializer\AccessType("public_method")
 * @author plfort
 */
class SubsonicResult extends TrackResult
{

    protected $shareable=false;

    public function __construct(){
        parent::__construct();
        
    }


    public function setUrl($url)
    {
        $this->pluginProperties['url'] =$url;
    }

}
