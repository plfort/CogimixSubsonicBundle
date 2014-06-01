<?php
namespace Cogipix\CogimixSubsonicBundle\Form;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
/**
 *
 * @author plfort - Cogipix
 *
 */
class SubsonicServerInfoFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add('name', 'text', array(
                'label' => 'Display name'))
        ->add('alias', 'text', array(
                        'label' => 'Unique alias'))
        ->add('username','text',array('label'=>'Username','required'=>false))
        ->add('password','text',array('label'=>'Password','required'=>false))
       ->add('endPointUrl','text',array('label'=>'URL'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo',
                'validation_groups' => function(FormInterface $form) {
                                $default = array('Create','CreateWithAuth');
                                return $default;
                            },
        ));
    }

    public function getName() {
        return 'subsonic_server_create_form';
    }
}