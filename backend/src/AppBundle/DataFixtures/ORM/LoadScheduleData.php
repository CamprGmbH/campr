<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Schedule;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Insert database entries for Schedule entity.
 */
class LoadScheduleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 2; ++$i) {
            $schedule = (new Schedule())
                ->setName('schedule'.$i)
            ;
            $manager->persist($schedule);
            $this->setReference('schedule'.$i, $schedule);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
