<?php
namespace Cogipix\CogimixSubsonicBundle\Form;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @author plfort - Cogipix
 *
 */
use Symfony\Component\Form\FormBuilderInterface;

class SubsonicServerInfoEditFormType extends SubsonicServerInfoFormType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->remove('alias');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo',
            'validation_groups' => function(FormInterface $form) {
                $default = array('Edit','CreateWithAuth');
                return $default;
            },
        ));
    }

}