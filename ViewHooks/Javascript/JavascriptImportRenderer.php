<?php
namespace Cogipix\CogimixSubsonicBundle\ViewHooks\Javascript;
use Cogipix\CogimixCommonBundle\ViewHooks\Javascript\JavascriptImportInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
class JavascriptImportRenderer implements JavascriptImportInterface
{

    public function getJavascriptImportTemplate()
    {
        return 'CogimixSubsonicBundle::js.html.twig';
    }

}
