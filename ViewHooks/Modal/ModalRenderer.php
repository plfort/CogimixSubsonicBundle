<?php
namespace Cogipix\CogimixSubsonicBundle\ViewHooks\Modal;

use Cogipix\CogimixCommonBundle\ViewHooks\Modal\ModalItemInterface;

class ModalRenderer implements ModalItemInterface
{

    public function getModalTemplate()
    {
        return 'CogimixSubsonicBundle:Modal:modals.html.twig';

    }

}
