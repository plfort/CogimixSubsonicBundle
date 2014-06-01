<?php
namespace Cogipix\CogimixSubsonicBundle\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo;

class SubsonicPluginFactory{

    private $container;

    public function __construct(ContainerInterface $container){

        $this->container=$container;
    }

    public function createSubsonicPlugin(SubsonicServerInfo $subsonicServerInfo){

        $customProviderPlugin = new SubsonicMusicSearch($this->container->get('cogimix.subsonic.result_builder'));
        $customProviderPlugin->setLogger($this->container->get('logger'));
        $customProviderPlugin->setSubsonicServerInfo($subsonicServerInfo);
        $customProviderPlugin->setSerializer($this->container->get('jms_serializer'));
       return $customProviderPlugin;
    }
}