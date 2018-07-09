<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Info;
use AppBundle\Entity\Meeting;
use AppBundle\Entity\Project;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

class InfoRepository extends BaseRepository
{
    /**
     * @param Project      $project
     * @param ParameterBag $filters
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderByProjectAndFilters(Project $project, ParameterBag $filters)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->andWhere(
            $qb->expr()->eq(
                'i.project',
                $project->getId()
            )
        );

        $infoCategory = $this->getIntParam($filters, 'info_category', 0);
        $user = $this->getIntParam($filters, 'user', 0);

        if ($infoCategory) {
            $qb->andWhere(
                $qb->expr()->in(
                    'i.infoCategory',
                    (array) $infoCategory
                )
            );
        }

        if ($user) {
            $qb->innerJoin('i.responsibility', 'u');
            $qb->andWhere(
                $qb->expr()->in(
                    'u.id',
                    (array) $user
                )
            );
        }

        return $qb;
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function getAllForStatusReport(Project $project)
    {
        $qb = $this->createQueryBuilder('o');

        $date = new \DateTime('-6 days');

        return $qb
            ->andWhere('o.project = :project')
            ->andWhere('o.expiresAt >= :date')
            ->setParameter('date', $date)
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Meeting $meeting
     *
     * @return Info[]
     */
    public function findOpenAndNotExpiredByMeeting(Meeting $meeting)
    {
        $qb = $this->createQueryBuilder('o');

        $date = new \DateTime('-6 days');

        $qb
            ->andWhere('o.project = :project')
            ->andWhere('o.expiresAt >= :date')
            ->setParameter('date', $date)
            ->setParameter('project', $meeting->getProject())
        ;

        if ($meeting->getId()) {
            $qb->andWhere($qb->expr()->neq('o.meeting', $meeting->getId()));
        }

        return $qb->getQuery()->getResult();
    }
}
