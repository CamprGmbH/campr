<?php

namespace AppBundle\Services;

use AppBundle\Entity\Assignment;
use AppBundle\Entity\Timephase;
use AppBundle\Entity\WorkPackage;
use AppBundle\Entity\WorkPackageProjectWorkCostType;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Project;
use AppBundle\Entity\Calendar;
use AppBundle\Entity\Day;
use AppBundle\Entity\WorkingTime;
use AppBundle\Utils\ImportConstants;

class ImportService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function checkIsDate($string)
    {
        return preg_match('/(?<!\d)\d{4}-\d{2}-\d{2}(?!\d)/', $string);
    }

    public function importProjects($content)
    {
        $xml = new \SimpleXMLElement($content);

        $project = new Project();
        foreach ($xml->children() as $tag => $element) {
            if (array_key_exists($tag, ImportConstants::PROJECT_KEY_FUNCTION)) {
                if ($tag === ImportConstants::PROJECT_NAME_TAG) {
                    if ($this->em->getRepository(Project::class)->findByName((string) $element)) {
                        return;
                    }
                }

                $action = ImportConstants::PROJECT_KEY_FUNCTION[$tag];
                if (is_callable([$project, $action])) {
                    if ($this->checkIsDate((string) $element)) {
                        $date = str_replace('T', ' ', (string) $element);
                        $project->$action(new \DateTime($date));
                    } else {
                        $project->$action((string) $element);
                    }
                }
            }

            switch ($tag) {
                case ImportConstants::CALENDARS_TAG:
                    $this->importCalendars($project, (array) $element);
                    break;
                case ImportConstants::TASKS_TAG:
                    $this->importWorkPackages($project, (array) $element);
                    break;
                case ImportConstants::RESOURCES_TAG:
                    $this->importWorkPackageProjectWorkCostTypes((array) $element);
                    break;
                case ImportConstants::ASSIGNMENTS_TAG:
                    $this->importAssignments((array) $element);
                    break;
            }
        }
        $this->em->persist($project);
        $this->em->flush();
    }

    public function importCalendars($project, $calendars)
    {
        foreach ($calendars['Calendar'] as $calendar) {
            $newCalendar = new Calendar();
            foreach ((array) $calendar as $calendarTag => $element) {
                if (array_key_exists($calendarTag, ImportConstants::CALENDAR_KEY_FUNCTION)) {
                    $action = ImportConstants::CALENDAR_KEY_FUNCTION[$calendarTag];
                    if (is_callable([$newCalendar, $action])) {
                        $newCalendar->$action((string) $element);
                    }
                }
                if ($calendarTag == ImportConstants::BASE_CALENDAR_TAG && $element == '-1') {
                    $baseCalendar = $this
                        ->em
                        ->getRepository(Calendar::class)
                        ->find((int) $element)
                    ;
                    if ($baseCalendar) {
                        $newCalendar->setParent($baseCalendar);
                    }
                }

                if ($calendarTag === ImportConstants::WEEKDAYS_TAG) {
                    $this->importDays($newCalendar, (array) $element);
                }
            }

            $newCalendar->setProject($project);
            $this->em->persist($newCalendar);
        }
        $this->em->flush();
    }

    public function importDays($calendar, $days)
    {
        foreach ($days['WeekDay'] as $day) {
            $newDay = new Day();
            foreach ((array) $day as $dayTag => $element) {
                if (array_key_exists($dayTag, ImportConstants::DAY_KEY_FUNCTION)) {
                    $action = ImportConstants::DAY_KEY_FUNCTION[$dayTag];
                    if (is_callable([$newDay, $action])) {
                        $newDay->$action((int) $element);
                    }
                }

                if ($dayTag === ImportConstants::WORKING_TIMES_TAG) {
                    $this->importWorkingTimes($newDay, (array) $element);
                }
                $newDay->setCalendar($calendar);
                $this->em->persist($newDay);
            }
        }
        $this->em->flush();
    }

    public function importWorkingTimes($day, $workingTimes)
    {
        foreach ($workingTimes['WorkingTime'] as $workingTime) {
            $newWorkingTime = new WorkingTime();
            foreach ((array) $workingTime as $workingTag => $element) {
                if (array_key_exists($workingTag, ImportConstants::WORKTIME_KEY_FUNCTION)) {
                    $action = ImportConstants::WORKTIME_KEY_FUNCTION[$workingTag];
                    if (is_callable([$newWorkingTime, $action])) {
                        $newWorkingTime->$action(new \DateTime($element));
                    }
                }
                $newWorkingTime->setDay($day);
                $this->em->persist($newWorkingTime);
            }
        }
        $this->em->flush();
    }

    public function importWorkPackages($project, $workPackages)
    {
        foreach ($workPackages['Task'] as $workPackage) {
            $newWorkPackage = new WorkPackage();
            foreach ((array) $workPackage as $workPackageTag => $element) {
                if (array_key_exists($workPackageTag, ImportConstants::WORKPACKAGE_KEY_FUNCTION)) {
                    $action = ImportConstants::WORKPACKAGE_KEY_FUNCTION[$workPackageTag];
                    if (is_callable([$newWorkPackage, $action])) {
                        if ($this->checkIsDate((string) $element)) {
                            $date = str_replace('T', ' ', (string) $element);
                            $newWorkPackage->$action(new \DateTime($date));
                        } else {
                            $newWorkPackage->$action((string) $element);
                        }
                    }
                }

                if ($workPackageTag == ImportConstants::UID) {
                    $workPackage = $this
                        ->em
                        ->getRepository(WorkPackage::class)
                        ->find((int) $element)
                    ;

                    if ($workPackage) {
                        $newWorkPackage->setParent($workPackage);
                    }
                }

                if ($workPackageTag == ImportConstants::CALENDAR_UID_TAG) {
                    $calendar = $this
                        ->em
                        ->getRepository(Calendar::class)
                        ->find((int) $element)
                    ;

                    if ($calendar) {
                        $newWorkPackage->setCalendar($calendar);
                    }
                }

                $newWorkPackage->setProject($project);
                $this->em->persist($newWorkPackage);
            }
        }
        $this->em->flush();
    }

    public function importWorkPackageProjectWorkCostTypes($workPackagesProjectWorkCostTypes)
    {
        foreach ($workPackagesProjectWorkCostTypes['Resource'] as $workPackagesProjectWorkCostType) {
            $newWorkPackagesProjectWorkCostType = new WorkPackageProjectWorkCostType();
            foreach ((array) $workPackagesProjectWorkCostType as $workPackageTag => $element) {
                if (array_key_exists($workPackageTag, ImportConstants::WPPWCT_KEY_FUNCTION)) {
                    $action = ImportConstants::WPPWCT_KEY_FUNCTION[$workPackageTag];
                    if (is_callable([$newWorkPackagesProjectWorkCostType, $action])) {
                        if ($this->checkIsDate((string) $element)) {
                            $date = str_replace('T', ' ', (string) $element);
                            $newWorkPackagesProjectWorkCostType->$action(new \DateTime($date));
                        } else {
                            $newWorkPackagesProjectWorkCostType->$action((string) $element);
                        }
                    }
                }

                if ($workPackageTag == ImportConstants::CALENDAR_UID_TAG) {
                    $calendar = $this
                        ->em
                        ->getRepository(Calendar::class)
                        ->find((int) $element)
                    ;
                    if ($calendar) {
                        $newWorkPackagesProjectWorkCostType->setCalendar($calendar);
                    }
                }
                $this->em->persist($newWorkPackagesProjectWorkCostType);
            }
        }
        $this->em->flush();
    }

    public function importAssignments($assignments)
    {
        foreach ($assignments['Assignment'] as $assignment) {
            $newAssignment = new Assignment();
            foreach ((array) $assignment as $assignmentTag => $element) {
                if (array_key_exists($assignmentTag, ImportConstants::ASSIGNMENT_KEY_FUNCTION)) {
                    $action = ImportConstants::ASSIGNMENT_KEY_FUNCTION[$assignmentTag];
                    if (is_callable([$newAssignment, $action])) {
                        if ($this->checkIsDate((string) $element)) {
                            $date = str_replace('T', ' ', (string) $element);
                            $newAssignment->$action(new \DateTime($date));
                        } else {
                            $newAssignment->$action((string) $element);
                        }
                    }
                }

                if ($assignmentTag == ImportConstants::TASK_UID_TAG) {
                    $workPackage = $this
                        ->em
                        ->getRepository(WorkPackage::class)
                        ->find((int) $element)
                    ;

                    if ($workPackage) {
                        $newAssignment->setWorkPackage($workPackage);
                    }
                }

                if ($assignmentTag == ImportConstants::RESOURCE_UID_TAG) {
                    $workPackageProjectWorkCostType = $this
                        ->em
                        ->getRepository(WorkPackageProjectWorkCostType::class)
                        ->find((int) $element)
                    ;
                    if ($workPackageProjectWorkCostType) {
                        $newAssignment->setWorkPackageProjectWorkCostType($workPackageProjectWorkCostType);
                    }
                }

                if ($assignmentTag == ImportConstants::TIMEPHASED_TAG) {
                    $this->importTimephased($newAssignment, (array) $element);
                }
                $this->em->persist($newAssignment);
            }
        }
        $this->em->flush();
    }

    public function importTimephased($assignment, $timephasedData)
    {
        foreach ((array) $timephasedData as $timephased) {
            $newTimephase = new Timephase();
            foreach ((array) $timephased as $timephasedTag => $element) {
                if (array_key_exists($timephasedTag, ImportConstants::TIMEPHASE_KEY_FUNCTION)) {
                    $action = ImportConstants::TIMEPHASE_KEY_FUNCTION[$timephasedTag];
                    if (is_callable([$newTimephase, $action])) {
                        if ($this->checkIsDate((string) $element)) {
                            $date = str_replace('T', ' ', (string) $element);
                            $newTimephase->$action(new \DateTime($date));
                        } else {
                            $newTimephase->$action((string) $element);
                        }
                    }
                }
                $newTimephase->setAssignment($assignment);
            }
            $this->em->persist($newTimephase);
        }

        $this->em->flush();
    }
}
