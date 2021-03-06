<?php

namespace AppBundle\Entity;

use Component\Resource\Cloner\CloneableInterface;
use Component\Resource\Model\ResourceInterface;
use Component\Resource\Model\ResponsibilityAwareInterface;
use Component\Resource\Model\TimestampableInterface;
use Component\Resource\Model\TimestampableTrait;
use Component\Media\MediasAwareInterface;
use Component\Project\ProjectAwareInterface;
use Component\Project\ProjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Decision.
 *
 * @ORM\Table(name="decision")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DecisionRepository")
 */
class Decision implements TimestampableInterface, ResponsibilityAwareInterface, ProjectAwareInterface, MediasAwareInterface, ResourceInterface, CloneableInterface
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
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="decisions")
     * @ORM\JoinColumn(name="project_id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var Meeting|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DistributionList", inversedBy="decisions")
     * @ORM\JoinColumn(name="distribution_list_id", onDelete="CASCADE")
     * Assert\NotBlank(message="not_blank.distribution_list")
     */
    private $distributionList;

    /**
     * @var Meeting|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Meeting", inversedBy="decisions")
     * @ORM\JoinColumn(name="meeting_id", onDelete="CASCADE")
     */
    private $meeting;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="not_blank.title")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
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
     * @var DecisionCategory|null
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DecisionCategory")
     * @ORM\JoinColumn(name="decision_category_id", onDelete="CASCADE")
     */
    private $decisionCategory;

    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
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
     * @var bool
     *
     * @ORM\Column(name="done", type="boolean", nullable=false, options={"default": false})
     */
    private $done;

    /**
     * @var \DateTime
     *
     * @Serializer\Exclude()
     * @Gedmo\Timestampable(on="change", field="done", value="1")
     *
     * @ORM\Column(name="done_at", type="datetime", nullable=true)
     */
    private $doneAt;

    /**
     * @var Media[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Media", inversedBy="decisions", cascade={"all"})
     * @ORM\JoinTable(
     *     name="decision_media",
     *     joinColumns={
     *         @ORM\JoinColumn(name="decision_id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="media_id", onDelete="CASCADE")
     *     }
     * )
     */
    private $medias;

    /**
     * Decision constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->done = false;
        $this->medias = new ArrayCollection();
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
     * @return Decision
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
     * @return Decision
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
     * @return Decision
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
     * @return Decision
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
     * @return Decision
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
     * @return Decision
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
     * @return Decision
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
     * @return DecisionCategory|null
     */
    public function getDecisionCategory()
    {
        return $this->decisionCategory;
    }

    /**
     * @param DecisionCategory|null $decisionCategory
     */
    public function setDecisionCategory(DecisionCategory $decisionCategory = null)
    {
        $this->decisionCategory = $decisionCategory;

        return $this;
    }

    /**
     * Returns decision category id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("decisionCategory")
     *
     * @return string
     */
    public function getDecisionCategoryId()
    {
        return $this->decisionCategory ? $this->decisionCategory->getId() : null;
    }

    /**
     * Returns decision category name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("decisionCategoryName")
     *
     * @return string
     */
    public function getDecisionCategoryName()
    {
        return $this->decisionCategory ? $this->decisionCategory->getName() : null;
    }

    /**
     * @Serializer\VirtualProperty()
     *
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     */
    public function setDone(bool $done): void
    {
        $this->done = $done;
    }

    /**
     * @return \DateTime|null
     */
    public function getDoneAt()
    {
        return $this->doneAt;
    }

    /**
     * @param \DateTime $doneAt
     */
    public function setDoneAt(\DateTime $doneAt = null)
    {
        $this->doneAt = $doneAt;
    }

    /**
     * @param Media $media
     *
     * @return Decision
     */
    public function addMedia(Media $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return Decision
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);

        return $this;
    }

    /**
     * @return Media[]|ArrayCollection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param Media[]|ArrayCollection $medias
     */
    public function setMedias($medias)
    {
        foreach ($medias as $media) {
            $media->addDecision($this);
        }

        $this->medias = $medias;
    }
}
