<?php

namespace AppBundle\Entity;

use Component\Project\ProjectAwareInterface;
use Component\Project\ProjectInterface;
use Component\Resource\Cloner\CloneableInterface;
use Component\Resource\Model\ResourceInterface;
use Component\Resource\Model\TimestampableInterface;
use Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Communication.
 *
 * @ORM\Table(name="communication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommunicationRepository")
 */
class Communication implements ResourceInterface, ProjectAwareInterface, CloneableInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Project
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="meeting_name", type="string", length=255)
     */
    private $meetingName;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(
     *     name="communication_participant",
     *     joinColumns={
     *         @ORM\JoinColumn(name="communication_id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="user_id", onDelete="CASCADE")
     *     }
     * )
     */
    private $participants;

    /**
     * @var Schedule
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Schedule")
     * @ORM\JoinColumn(name="schedule_id", onDelete="CASCADE")
     */
    private $schedule;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var \DateTime
     *
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     *
     * @Serializer\Exclude()
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Communication constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->participants = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set meetingName.
     *
     * @param string $meetingName
     *
     * @return Communication
     */
    public function setMeetingName($meetingName)
    {
        $this->meetingName = $meetingName;

        return $this;
    }

    /**
     * Get meetingName.
     *
     * @return string
     */
    public function getMeetingName()
    {
        return $this->meetingName;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Communication
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set location.
     *
     * @param string $location
     *
     * @return Communication
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set project.
     *
     * @param ProjectInterface $project
     *
     * @return Communication
     */
    public function setProject(ProjectInterface $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project.
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Returns project name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("projectName")
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->project ? $this->project->getName() : null;
    }

    /**
     * Add participant.
     *
     * @param User $participant
     *
     * @return Communication
     */
    public function addParticipant(User $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant.
     *
     * @param User $participant
     */
    public function removeParticipant(User $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * Get participants.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set schedule.
     *
     * @param Schedule $schedule
     *
     * @return Communication
     */
    public function setSchedule(Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule.
     *
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("meetingName")
     *
     * @return string
     */
    public function getMeetingNameSerialized()
    {
        return $this->meetingName ? $this->meetingName : null;
    }
}
