<?php

namespace Fly\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AirportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city_name')
            ->add('city_code')
            ->add('airport_code')
            ->add('airport_name')
            ->add('country_name')
            ->add('country_abbrev', 'country')
            ->add('world_area_code')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\PlatformBundle\Entity\Airport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_platformbundle_airport';
    }
}
