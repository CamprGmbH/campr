<?php

namespace AppBundle\Entity;

use Component\Resource\Cloner\CloneableInterface;
use Component\Resource\Model\ResourceInterface;
use Component\Resource\Model\ResponsibilityAwareInterface;
use Component\Resource\Model\TimestampableInterface;
use Component\Resource\Model\TimestampableTrait;
use Component\Project\ProjectAwareInterface;
use Component\Project\ProjectInterface;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoRepository")
 */
class Todo implements TimestampableInterface, ResponsibilityAwareInterface, ProjectAwareInterface, ResourceInterface, CloneableInterface
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
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="todos")
     * @ORM\JoinColumn(name="project_id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var TodoCategory|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TodoCategory")
     * @ORM\JoinColumn(name="todo_category_id", onDelete="CASCADE")
     */
    private $todoCategory;

    /**
     * @var Meeting|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DistributionList", inversedBy="todos")
     * @ORM\JoinColumn(name="distribution_list_id", onDelete="CASCADE")
     * Assert\NotBlank(message="not_blank.distribution_list")
     */
    private $distributionList;

    /**
     * @var Meeting|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Meeting", inversedBy="todos")
     * @ORM\JoinColumn(name="meeting_id", onDelete="CASCADE")
     */
    private $meeting;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="not_blank.topic")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="not_blank.description")
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_in_status_report", type="boolean", nullable=false, options={"default"=0})
     */
    private $showInStatusReport = false;

    /**
     * @var User|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="responsibility_id", onDelete="SET NULL")
     */
    private $responsibility;

    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     *
     * @ORM\Column(name="due_date", type="date", nullable=true)
     */
    private $dueDate;

    /**
     * @var TodoStatus
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TodoStatus")
     * @ORM\JoinColumn(name="status_id", onDelete="CASCADE")
     */
    private $status;

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
     * @var \DateTime
     *
     * @Serializer\Exclude()
     * @Gedmo\Timestampable(on="change", field="status")
     *
     * @ORM\Column(name="status_updated_at", type="datetime", nullable=false)
     */
    private $statusUpdatedAt;

    /**
     * Todo constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->statusUpdatedAt = new \DateTime();
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
     * Set title.
     *
     * @param string $title
     *
     * @return Todo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Todo
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set showInStatusReport.
     *
     * @param bool $showInStatusReport
     *
     * @return Todo
     */
    public function setShowInStatusReport($showInStatusReport)
    {
        $this->showInStatusReport = $showInStatusReport;

        return $this;
    }

    /**
     * Get showInStatusReport.
     *
     * @return bool
     */
    public function getShowInStatusReport()
    {
        return $this->showInStatusReport;
    }

    /**
     * Set dueDate.
     *
     * @param \DateTime $dueDate
     *
     * @return Todo
     */
    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate.
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set project.
     *
     * @param ProjectInterface $project
     *
     * @return Todo
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
     * Set meeting.
     *
     * @param Meeting $meeting
     *
     * @return Todo
     */
    public function setMeeting(Meeting $meeting = null)
    {
        $this->meeting = $meeting;

        if ($meeting && $meeting->getProject()) {
            $this->project = $meeting->getProject();
        } else {
            $this->project = null;
        }

        return $this;
    }

    /**
     * Get meeting.
     *
     * @return Meeting
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * Set meeting.
     *
     * @param DistributionList $distributionList
     */
    public function setDistributionList(DistributionList $distributionList = null)
    {
        $this->distributionList = $distributionList;
    }

    /**
     * Get meeting.
     *
     * @return Meeting
     */
    public function getDistributionList()
    {
        return $this->distributionList;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("distributionList")
     *
     * @return int|null
     */
    public function getDistributionListId()
    {
        return $this->distributionList
            ? $this->distributionList->getId()
            : null;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("distributionListName")
     *
     * @return string|null
     */
    public function getDistributionListName()
    {
        return $this->distributionList
            ? $this->distributionList->getName()
            : null;
    }

    /**
     * Set responsibility.
     *
     * @param User $responsibility
     *
     * @return Todo
     */
    public function setResponsibility(User $responsibility = null)
    {
        $this->responsibility = $responsibility;

        return $this;
    }

    /**
     * Get responsibility.
     *
     * @return User
     */
    public function getResponsibility()
    {
        return $this->responsibility;
    }

    /**
     * Set status.
     *
     * @param TodoStatus $status
     *
     * @return Todo
     */
    public function setStatus(TodoStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return TodoStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns status id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("status")
     *
     * @return string
     */
    public function getStatusId()
    {
        return $this->status ? $this->status->getId() : null;
    }

    /**
     * Returns status name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("statusName")
     *
     * @return string
     */
    public function getStatusName()
    {
        return $this->status ? $this->status->getName() : null;
    }

    /**
     * Returns meeting id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("meeting")
     *
     * @return string
     */
    public function getMeetingId()
    {
        return $this->meeting ? $this->meeting->getId() : null;
    }

    /**
     * Returns meeting name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("meetingName")
     *
     * @return string
     */
    public function getMeetingName()
    {
        return $this->meeting ? $this->meeting->getName() : null;
    }

    /**
     * Returns project id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("project")
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->project ? $this->project->getId() : null;
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
     * Returns the responsibility username.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("responsibility")
     *
     * @return string
     */
    public function getResponsibilityId()
    {
        return $this->responsibility ? $this->responsibility->getId() : null;
    }

    /**
     * Returns the responsibility username.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("responsibilityFullName")
     *
     * @return string
     */
    public function getResponsibilityFullName()
    {
        return $this->responsibility ? $this->responsibility->getFullName() : null;
    }

    /**
     * @return TodoCategory|null
     */
    public function getTodoCategory()
    {
        return $this->todoCategory;
    }

    /**
     * @param TodoCategory|null $todoCategory
     */
    public function setTodoCategory(TodoCategory $todoCategory)
    {
        $this->todoCategory = $todoCategory;

        return $this;
    }

    /**
     * Returns todo category id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("todoCategory")
     *
     * @return string
     */
    public function getTodoCategoryId()
    {
        return $this->todoCategory ? $this->todoCategory->getId() : null;
    }

    /**
     * Returns todo category name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("todoCategoryName")
     *
     * @return string
     */
    public function getTodoCategoryName()
    {
        return $this->todoCategory ? $this->todoCategory->getName() : null;
    }

    /**
     * @return \DateTime
     */
    public function getStatusUpdatedAt(): \DateTime
    {
        return $this->statusUpdatedAt;
    }

    /**
     * @param \DateTime $statusUpdatedAt
     */
    public function setStatusUpdatedAt(\DateTime $statusUpdatedAt = null)
    {
        $this->statusUpdatedAt = $statusUpdatedAt;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        $status = $this->getStatus();
        if (!$status) {
            return false;
        }

        return $status->isFinished();
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        $status = $this->getStatus();
        if (!$status) {
            return false;
        }

        return $status->isOpen();
    }
}
