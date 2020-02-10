<?php

namespace Fly\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AccItemType extends AbstractType
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
                    'class'=>'hidden'
                ]
            ])
            ->add('checkout','date',[
                'label'=>false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr'=>[
                    'class'=>'hidden'
                ]
            ])
            ->add('duration','integer',[
                'attr'=>[
                    'min'=>1
                ]
            ])
            ->add('acc',null,['label'=>false,'attr'=>['class'=>'hidden']])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\PlatformBundle\Entity\AccItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_platformbundle_accitem';
    }
}
