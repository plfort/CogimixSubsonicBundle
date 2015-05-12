<?php
namespace Cogipix\CogimixSubsonicBundle\Services;

use Cogipix\CogimixCommonBundle\Plugin\PluginProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Cogipix\CogimixCommonBundle\ViewHooks\Playlist\PlaylistRendererListInterface;
use Cogipix\CogimixSubsonicBundle\ViewHooks\Playlist\PlaylistRenderer;


class SubsonicPluginProvider implements PluginProviderInterface, PlaylistRendererListInterface
{

    private $om;

    private $tokenStorage;

    protected $plugins = array();

    private $playlistRenderers=array();

    protected $pluginProviders;

    private $pluginFactory;

    public function __construct(ObjectManager $om, TokenStorageInterface $tokenStorageInterface, SubsonicPluginFactory $factory)
    {
        $this->om = $om;
        $this->tokenStorage = $tokenStorageInterface;
        $this->pluginFactory = $factory;
    }

    public function getAvailablePlugins()
    {
        $user = $this->getCurrentUser();
        if ($user != null) {
            if (empty($this->plugins)) {
                $subsonicServerInfos = $this->om->getRepository('CogimixSubsonicBundle:SubsonicServerInfo')->findByUser($user);
                if (! empty($subsonicServerInfos)) {
                    foreach ($subsonicServerInfos as $subsonicServerInfo) {

                        $this->plugins[$subsonicServerInfo->getAlias()] = $this->pluginFactory->createSubsonicPlugin($subsonicServerInfo);

                    }
                }
            }
        }
        return $this->plugins;
    }

    public function addPlaylistRenderer($playlistRenderer)
    {

        $this->playlistRenderers[] =$playlistRenderer;
    }

    public function getPlaylistRenderers()
    {
        foreach($this->getAvailablePlugins() as $plugin){
            $this->addPlaylistRenderer(new PlaylistRenderer($plugin));
        }

        return $this->playlistRenderers;
    }

    public function getPluginChoiceList()
    {
        $choices = array();
        if (! empty($this->plugins)) {
            foreach ($this->plugins as $alias => $plugin) {
                $choices[$alias] = $plugin->getName();
            }
        }
        return $choices;
    }

    protected function getCurrentUser()
    {
        $user = null;
        $token = $this->tokenStorage->getToken();
        if($token){
            $user = $token->getUser();
        }

        if ($user instanceof \FOS\UserBundle\Model\UserInterface) {
            return $user;
        }
        return null;
    }

    public function getAlias()
    {
        return 'subsonicpluginprovider';
    }
}