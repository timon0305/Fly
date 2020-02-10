<?php

namespace Fly\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ActType extends AbstractType
{
    private $currencyList = [];


    public function __construct()
    {
        $intlCurrencyNames = Intl::getCurrencyBundle()->getCurrencyNames();
        foreach($intlCurrencyNames as $k=>$v){
            $curSymbol = Intl::getCurrencyBundle()->getCurrencySymbol($k);
            $curval = ($curSymbol != $k)?$curSymbol.' '.$k : $k;
            $this->currencyList[$k]=$curval;
        }
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price','number')
            ->add('currency','currency',[
                'choices' => $this->currencyList,
                'preferred_choices' => array('USD', 'EUR')
            ])
            ->add('address','hidden')
            ->add('lat','hidden')
            ->add('lng','hidden')
//            ->add('created')
//            ->add('updated')
//            ->add('actCategory')
            ->add('actItem','collection', array(
                    'type' => new ActItemType()
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\PlatformBundle\Entity\Act'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fly_platformbundle_act';
    }
}
