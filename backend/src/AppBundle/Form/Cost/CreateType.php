<?php

namespace AppBundle\Form\Cost;

use AppBundle\Entity\Cost;
use AppBundle\Entity\Project;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Unit;
use AppBundle\Entity\WorkPackage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
            ])
            ->add('workPackage', EntityType::class, [
                'class' => WorkPackage::class,
                'choice_label' => 'name',
            ])
            ->add('resource', EntityType::class, [
                'class' => Resource::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class)
            ->add('type', IntegerType::class)
            ->add('expenseType', IntegerType::class)
            ->add('rate', NumberType::class)
            ->add('quantity', NumberType::class)
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
            ])
            ->add('duration', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cost::class,
            'csrf_protection' => false,
        ]);
    }
}
