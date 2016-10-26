<?php

namespace AppBundle\Form\MeetingAgenda;

use AppBundle\Entity\MeetingAgenda;
use AppBundle\Entity\Meeting;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meeting', EntityType::class, [
                'class' => Meeting::class,
                'choice_label' => 'name',
                'translation_domain' => 'admin',
            ])
            ->add('topic', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.constraints.meeting_agenda.topic.not_blank',
                    ]),
                ],
            ])
            ->add('responsibility', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'translation_domain' => 'admin',
            ])
            ->add('start', TimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('end', TimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('duration', TimeType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MeetingAgenda::class,
        ]);
    }
}
