<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

trait UserSortingTrait
{
    /**
     * @param array        $orderBy
     * @param QueryBuilder $qb
     */
    protected function setOrder(array &$orderBy, QueryBuilder $qb)
    {
        foreach ($orderBy as $field => $dir) {
            switch ($field) {
                case 'responsibilityFullName':
                    $qb->leftJoin('q.responsibility', 'r');
                    $qb->addSelect('CONCAT(r.firstName, \' \', r.lastName) AS HIDDEN full_name');
                    $qb->orderBy('full_name', $dir);
                    unset($orderBy['responsibilityFullName']);
                    break;
                case 'userFullName':
                    if (!in_array('u', $qb->getAllAliases())) {
                        $qb->leftJoin('q.user', 'u');
                    }
                    $qb->addSelect('CONCAT(u.firstName, \' \', u.lastName) AS HIDDEN full_name');
                    $qb->orderBy('full_name', $dir);
                    unset($orderBy['userFullName']);
                    break;
                case 'createdByFullName':
                    $qb->leftJoin('q.createdBy', 'c');
                    $qb->addSelect('CONCAT(c.firstName, \' \', c.lastName) AS HIDDEN full_name');
                    $qb->orderBy('full_name', $dir);
                    unset($orderBy['createdByFullName']);
                    break;
                case 'userEmail':
                    $qb->orderBy('u.email', $dir);
                    unset($orderBy['userEmail']);
                    break;
                default:
                    continue;
            }
        }

        parent::setOrder($orderBy, $qb);
    }
}
