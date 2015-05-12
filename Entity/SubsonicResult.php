<?php
namespace Cogipix\CogimixSubsonicBundle\Entity;

use Cogipix\CogimixCommonBundle\Entity\Song;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;
/**
  * @JMSSerializer\AccessType("public_method")
 * @ORM\MappedSuperclass()
 * @author plfort
 */
class SubsonicResult extends Song
{

    protected $shareable=false;


    public function setUrl($url)
    {
        $this->pluginProperties['url'] =$url;
    }

}
