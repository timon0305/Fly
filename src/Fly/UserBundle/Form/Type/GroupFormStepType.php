<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fly\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fly\UserBundle\Form\DataTransformer\InvitationToCodeTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupFormStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder
//            ->add('description')
//            ->add('picture','file',['label'=>'Picture','required'=>false, 'data_class' => null])
            ->add('spirits',null,[
                'expanded'=>true,
                'multiple'=>true,
                'label'=>'What do you have in mind?'
            ])
            ->add('travelingWith',null,[
                'expanded'=>true,
                'multiple'=>true,
                'label'=>'Who are you trevelling with?',

            ])
            ->add('accomodations',null,[
                'expanded'=>true,
                'multiple'=>true,
                'label'=>'What kind of accomodation?'
            ])
            ->add('transportations',null,[
                'expanded'=>true,
                'multiple'=>true,
                'label'=>"What kind of transportation?"
            ])
//            ->add('activities',null,[
//                'expanded'=>true,
//                'multiple'=>true,
//                'label'=>'What kind of activities?'
//            ])
            ->add('is_wheretogo',null,['label'=>"We don't know yet, we'll decide!",'required'=>false])
            ->add('geoAddress','hidden')
            ->add('geoLat','hidden')
            ->add('geoLng','hidden')
//            ->add('city',null,['required'=>false])
//            ->add('country','country',[
//                'placeholder'=>'Select Country',
//                'required'=>false
//            ])
//            ->add('world_zones',null,[
//                'expanded'=>true,
//                'multiple'=>true,
//                'label'=>'Zone',
//                'required'=>false
//            ])
            ->add('is_whentogo',null,['label'=>"We don't know yet, we'll decide!",'required'=>false])
            ->add('departure_date',null,[
                'label'=>'Departure',
                'required'=>false,
                'widget' => 'single_text',
                'format' => 'MM/dd/yyyy',
                'attr'=>['class'=>'hidden']

            ])
//            ->add('departure_date_flexibility',null,['label'=>'Flexibility more or less','required'=>false])
            ->add('wayback_date',null,[
                'label'=>'Way back',
                'required'=>false,
                'widget' => 'single_text',
                'format' => 'MM/dd/yyyy',
                'attr'=>['class'=>'hidden']
            ])
//            ->add('wayback_date_flexibility',null,['label'=>'Flexibility more or less','required'=>false])
            ->add('expired_time',null,['label'=>"When does this group expire ?",'required'=>false])
            ->add('goal','collection',[
//                'data_class'=>null
                'type'=> new GoalsType(),
                'by_reference'=>false,
                'required'=>false,
                'label'=>false,
                'allow_add'    => true,
                'allow_delete' => true,

            ])
            ->add('invitation','collection',[
//                'data_class'=>null
                'type'=> new GroupInvitationNormalFormType(),
                'by_reference'=>false,
                'required'=>false,
                'label'=>false,
                'allow_add'    => true,

            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getParent()
    {
        return 'fos_user_group';
    }

    public function getName()
    {
        return 'fly_user_group_step';
    }
}
