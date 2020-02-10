<?php

namespace Fly\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserBasicInfoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name',null,['label'=>'First Name'])
            ->add('last_name',null,['label'=>'Last  Name'])
            ->add('gender','choice',[
                'label'=>'Gender',
                'choices' => ['Male'=>'Male','Female'=>'Female','Other'=>'Other']
            ])
            ->add('birthday','date',[
                'label'=>'Birthday',
                'widget'=>'single_text',
                'format'=>'dd/MM/yyyy'
            ])
            ->add('hometown',null,['label'=>'Home Town'])
            ->add('hometownFb','hidden',['label'=>false])
            ->add('location',null,['label'=>'Current city'])
            ->add('locationFb','hidden',['label'=>false])
            ->add('locationTw','hidden',['label'=>false])
            ->add('location',null,['label'=>'Current city'])
            ->add('mstatus','choice',[
                'label'=>'Martial Status',
                'choices' => ['Single'=>'Single','Married'=>'Married','Other'=>'Other']
            ])

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_userbundle_user';
    }
}
