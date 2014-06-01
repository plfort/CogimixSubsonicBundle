<?php
namespace Cogipix\CogimixSubsonicBundle\Services;


use Cogipix\CogimixCommonBundle\Plugin\PluginProviderInterface;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\SecurityContextInterface;

class SubsonicPluginProvider implements PluginProviderInterface{

    private $om;
    private $securityContext;
    protected $plugins = array();
    protected $pluginProviders;

    private $pluginFactory;

    public function __construct(ObjectManager $om,SecurityContextInterface $securityContext,SubsonicPluginFactory $factory){
        $this->om=$om;
        $this->securityContext=$securityContext;
        $this->pluginFactory=$factory;

    }

    public function getAvailablePlugins(){
     $user = $this->getCurrentUser();
     if($user!=null){
        $subsonicServerInfos=$this->om->getRepository('CogimixSubsonicBundle:SubsonicServerInfo')->findByUser($user);
        if(!empty($subsonicServerInfos)){
            foreach($subsonicServerInfos as $subsonicServerInfo){
                $this->plugins[$subsonicServerInfo->getAlias()]= $this->pluginFactory->createSubsonicPlugin($subsonicServerInfo);
            }
        }
     }
        return $this->plugins;
    }


    public function getPluginChoiceList()
    {
        $choices = array();
        if(!empty($this->plugins)){
            foreach($this->plugins as $alias=>$plugin){
                $choices[$alias] = $plugin->getName();
            }
        }
        return $choices;
    }


    protected function getCurrentUser() {
        $user = $this->securityContext->getToken()->getUser();
        if ($user instanceof \FOS\UserBundle\Model\UserInterface){
            return $user;
        }
        return null;
    }

    public function getAlias(){
        return 'subsonicpluginprovider';
    }
}