<?php

namespace Fly\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GoalsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',null,['label'=>'Title','required'=>true])
            ->add('goal_date','datetime',[
                'label'=>'Date',
                'widget'=>'single_text',
                'attr'=>['class'=>'goal-date-datepicker-val'],
                'format'=>'yyyy-MM-dd',
                'required'=>true
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\UserBundle\Entity\Goals'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_userbundle_goals';
    }
}
