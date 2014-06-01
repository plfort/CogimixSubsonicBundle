<?php
namespace Cogipix\CogimixSubsonicBundle\ViewHooks\Menu;


use Cogipix\CogimixCommonBundle\ViewHooks\Menu\MenuItemInterface;
use Cogipix\CogimixCommonBundle\ViewHooks\Menu\AbstractMenuItem;


class MenuItem  extends AbstractMenuItem{

    public function getMenuItemTemplate()
    {
          return 'CogimixSubsonicBundle:Menu:menu.html.twig';

    }

    public function getName(){
    	return 'subsonic';
    }
}