<?php
namespace Cogipix\CogimixSubsonicBundle\Form;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
use Symfony\Component\Form\FormBuilderInterface;

class SubsonicServerInfoEditFormType extends CustomProviderInfoFormType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->remove('alias');

    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo',
                'validation_groups' => function(FormInterface $form) {
                                $default = array('Edit','CreateWithAuth');
                                return $default;
                            },
        ));
    }

}