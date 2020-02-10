<?php

namespace Fly\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('checkin','date',[
                'label'=>false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr'=>[
                    'class'=>''
                ]
            ])
            ->add('checkout','date',[
                'label'=>false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr'=>[
                    'class'=>''
                ]
            ])
//            ->add('duration','integer',[
//                'attr'=>[
//                    'min'=>1,
//                    'class'=>'hidden'
//                ]
//            ])
////            ->add('act')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\PlatformBundle\Entity\ActItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_platformbundle_actitem';
    }
}
