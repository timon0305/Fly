<?php

namespace Fly\PlatformBundle\Form;

use Fly\PlatformBundle\Entity\FlyInsurance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class FlyOrderType extends AbstractType
{

    protected  $years;

    public function __construct()
    {
        $now = new \DateTime();
        $nowYear = (integer)$now->format('Y');
        $end = $nowYear - 12;
        $start = $nowYear - 100;

        $this->years = array_reverse(range($start,$end));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',null,[
                'required'=>true,
                'label'=>'First Name',
            ])
            ->add('familyname',null,[
                'required'=>true,
                'label'=>'Family Name',
            ])
//            ->add('email','email',[
//                'required'=>true,
//            ])
            ->add('date_of_birth','date',[
                'label'=>'Date of Birth',
//                'widget'=>'single_text',
                'html5'=>true,
                'attr'=>['class'=>'date-time-picker-order'],
                'format'=>'dd MMMM y',
                'required'=>true,
                'years'=>$this->years
            ])
//            ->add('outbound',)
//            ->add('inbound')
//            ->add('price')
            ->add('is_confirmed','checkbox',[
                'required'=>true,
                'label'=>'Im agree with terms and rules',
            ])
//            ->add('created')
//            ->add('updated')
            ->add('insurance','entity',[
                'class' => 'FlyPlatformBundle:FlyInsurance',
                'expanded'=>true,
                'multiple'=>false,
                'empty_data' => 'No,thanks',
                'required'=>false,
                'label'=>'Insurance',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
//                        ->select('i.insurance, i.name')
                        ->orderBy('i.insurance', 'ASC');
                },
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\PlatformBundle\Entity\FlyOrder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_platformbundle_flyorder';
    }
}
