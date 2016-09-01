<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Doctrine\ORM\Query\Expr\Select;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'Tytuł'))
            ->add('instructions', null, array('label' => 'Sposób przygotowania'))
            ->add('preparationTime', null, array('label' => 'Czas przygotownia [min]'))
            ->add('portions', null, array('label' => 'Ilość porcji'))
            ->add('category', 'entity',
                ['class' => 'AppBundle:Category'])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recipe'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_recipe';
    }
}
