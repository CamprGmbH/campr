<?php

namespace AppBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\Event\OnFlushEventArgs;
use AppBundle\Entity\Log;
use AppBundle\Entity\User;

class DBLogger
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uok = $em->getUnitOfWork();
        $logMetadata = $em->getClassMetadata(Log::class);

        foreach ($uok->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Log) {
                continue;
            }

            $changeSet = $uok->getEntityChangeSet($entity);

            $log = new Log();
            $log->setObjId($entity->getId());
            $log->setClass(get_class($entity));
            $oldValues = [];
            $newValues = [];
            foreach ($changeSet as $key => $value) {
                $oldValues[$key] = $value[0];
                $newValues[$key] = $value[1];
            }

            $log->setOldValue($this->normalizeValue($em, $oldValues));
            $log->setNewValue($this->normalizeValue($em, $newValues));
            if ($token = $this->tokenStorage->getToken()) {
                $user = $token->getUser();
                if ($user instanceof User) {
                    $user = $em
                        ->getRepository(User::class)
                        ->find($user->getId())
                    ;
                    $log->setUser($user);
                }
            }

            $uok->persist($log);
            $uok->computeChangeSet($logMetadata, $log);
        }
    }

    private function normalizeValue(\Doctrine\ORM\EntityManager $em, $value)
    {
        if (is_object($value)) {
            $class = get_class($value);
            try {
                $md = $em->getClassMetadata($class);

                return [
                    'class' => $md->getName(),
                    'id' => $value->getId(),
                ];
            } catch (\Doctrine\Common\Persistence\Mapping\MappingException $e) {
            }
        }

        return $value;
    }
}
