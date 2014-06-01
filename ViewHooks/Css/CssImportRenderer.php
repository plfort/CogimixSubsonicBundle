<?php
namespace Cogipix\CogimixSubsonicBundle\ViewHooks\Css;
use Cogipix\CogimixCommonBundle\ViewHooks\Css\CssImportInterface;


/**
 *
 * @author plfort - Cogipix
 *
 */
class CssImportRenderer implements CssImportInterface
{

    public function getCssImportTemplate()
    {
        return 'CogimixSubsonicBundle::css.html.twig';
    }

}
