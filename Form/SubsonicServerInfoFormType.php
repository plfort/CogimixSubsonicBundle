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
                'label' => 'cogimix.subsonic_server_info.name'))
        ->add('alias', 'text', array(
                        'label' => 'cogimix.subsonic_server_info.unique_alias'))
        ->add('username','text',array('label'=>'cogimix.subsonic_server_info.username','required'=>false))
        ->add('password','text',array('label'=>'cogimix.subsonic_server_info.password','required'=>false))
       ->add('endPointUrl','text',array('label'=>'cogimix.subsonic_server_info.url'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo',
                'validation_groups' => function(FormInterface $form) {
                                $default = array('Create');
                                return $default;
                            },
        ));
    }

    public function getName() {
        return 'subsonic_server_create_form';
    }
}