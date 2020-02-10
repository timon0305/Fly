<?php

namespace Fly\UserBundle\Form\Type;

use Fly\PlatformBundle\FlyPlatformBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feedCategory',null,[
                'required'=>false,
                'label'=>null,
                'empty_value'=>'Choose Category',
                'attr'=>['class'=>'feed-category']
            ])
            ->add('picture','hidden')
            ->add('description',null,[
                'label'=>'Comments',
                'attr'=>['class'=>'feed-content']
            ])
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\UserBundle\Entity\Feed'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_userbundle_feed';
    }
}
