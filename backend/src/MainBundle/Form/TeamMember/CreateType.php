<?php

namespace MainBundle\Form\TeamMember;

use AppBundle\Entity\TeamMember;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'query_builder' => function (EntityRepository $er) use ($entity) {
                    $team = $entity->getTeam();
                    $members = [];
                    if ($team && $team->getTeamMembers()) {
                        foreach ($team->getTeamMembers() as $member) {
                            $members[] = $member->getUser()->getId();
                        }
                    }
                    $qb = $er->createQueryBuilder('u');
                    if (!empty($members)) {
                        $qb
                            ->where($qb->expr()->notIn('u.id', ':ids'))
                            ->setParameter('ids', $members)
                        ;
                    }

                    return $qb;
                },
            ])
            ->add('roles', ChoiceType::class, array(
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'label.role.user' => 'ROLE_USER',
                    'label.role.admin' => 'ROLE_ADMIN',
                    'label.role.superadmin' => 'ROLE_SUPER_ADMIN',
                    'label.role.team_member' => 'ROLE_TEAM_MEMBER',
                    'label.role.team_owner' => 'ROLE_TEAM_OWNER',
                ],
                'translation_domain' => 'messages',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TeamMember::class,
        ]);
    }
}
