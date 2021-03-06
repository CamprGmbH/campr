<?php

namespace AppBundle\Entity;

use Component\Resource\Model\FileSystemAwareInterface;
use Component\Resource\Model\ResourceInterface;
use Component\Resource\Model\TimestampableInterface;
use Component\Resource\Model\TimestampableTrait;
use Component\User\Model\UserAwareInterface;
use Component\User\Model\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Media.
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 */
class Media implements FileSystemAwareInterface, UserAwareInterface, TimestampableInterface, ResourceInterface
{
    use TimestampableTrait;

    const DEFAULT_EXPIRE_DELTA = 60 * 60;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FileSystem
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="FileSystem", inversedBy="medias")
     * @ORM\JoinColumn(name="file_system_id", nullable=false, onDelete="CASCADE")
     */
    private $fileSystem;

    /**
     * @var ArrayCollection|Meeting[]
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Meeting", inversedBy="medias")
     * @ORM\JoinTable(
     *     name="media_meeting",
     *     joinColumns={
     *         @ORM\JoinColumn(name="media_id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="meeting_id", onDelete="CASCADE")
     *     }
     * )
     */
    private $meetings;

    /**
     * @var User
     *
     * @Serializer\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="medias")
     * @ORM\JoinColumn(name="user_id", onDelete="SET NULL")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=128, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="original_name", type="string", length=128, nullable=true)
     */
    private $originalName;

    /**
     * @var File
     *
     * @Serializer\Exclude()
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", length=128, nullable=false)
     */
    private $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(name="file_size", type="bigint", nullable=false)
     */
    private $fileSize;

    /**
     * @var WorkPackage[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\WorkPackage", mappedBy="medias")
     * @Serializer\Exclude()
     */
    private $workPackages;

    /**
     * @var WorkPackage[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Decision", mappedBy="medias")
     * @Serializer\Exclude()
     */
    private $decisions;

    /**
     * @var Measure[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Measure", mappedBy="medias")
     * @Serializer\Exclude()
     */
    private $measures;

    /**
     * @var MeasureComment[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\MeasureComment", mappedBy="medias")
     * @Serializer\Exclude()
     */
    private $measureComments;

    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * Media constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->meetings = new ArrayCollection();
        $this->workPackages = new ArrayCollection();
        $this->decisions = new ArrayCollection();
        $this->measures = new ArrayCollection();
        $this->measureComments = new ArrayCollection();
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
     * Set path.
     *
     * @param string $path
     *
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set mimeType.
     *
     * @param string $mimeType
     *
     * @return Media
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set fileSize.
     *
     * @param int $fileSize
     *
     * @return Media
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize.
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set fileSystem.
     *
     * @param FileSystem $fileSystem
     *
     * @return Media
     */
    public function setFileSystem(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;

        return $this;
    }

    /**
     * Get fileSystem.
     *
     * @return FileSystem
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * Returns FileSystem id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("fileSystem")
     *
     * @return string
     */
    public function getFileSystemId()
    {
        return $this->fileSystem ? $this->fileSystem->getId() : null;
    }

    /**
     * Returns FileSystem name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("fileSystemName")
     *
     * @return string
     */
    public function getFileSystemName()
    {
        return $this->fileSystem ? $this->fileSystem->getName() : null;
    }

    /**
     * Set user.
     *
     * @param UserInterface $user
     *
     * @return Media
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns User id.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("user")
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user ? $this->user->getId() : null;
    }

    /**
     * Returns User full name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("userFullName")
     *
     * @return string
     */
    public function getUserFullName()
    {
        return $this->user ? $this->user->getFullName() : null;
    }

    /**
     * Get file.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file.
     *
     * @param File $file
     *
     * @return Media
     */
    public function setFile($file)
    {
        if (!($file instanceof File)) {
            return;
        }

        $this->file = $file;

        return $this;
    }

    /**
     * Add meeting.
     *
     * @param Meeting $meeting
     *
     * @return Media
     */
    public function addMeeting(Meeting $meeting)
    {
        if ($this->meetings->contains($meeting)) {
            return $this;
        }

        $this->meetings->add($meeting);

        return $this;
    }

    /**
     * Remove meeting.
     *
     * @param Meeting $meeting
     */
    public function removeMeeting(Meeting $meeting)
    {
        $this->meetings->removeElement($meeting);
    }

    /**
     * Get meetings.
     *
     * @return ArrayCollection
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    /**
     * @param WorkPackage $workPackage
     *
     * @return Media
     */
    public function addWorkPackage(WorkPackage $workPackage)
    {
        $this->workPackages[] = $workPackage;
        $workPackage->addMedia($this);

        return $this;
    }

    /**
     * @param WorkPackage $workPackage
     *
     * @return Media
     */
    public function removeWorkPackage(WorkPackage $workPackage)
    {
        $this->workPackages->removeElement($workPackage);
        $workPackage->removeMedia($this);

        return $this;
    }

    /**
     * @return WorkPackage[]|ArrayCollection
     */
    public function getWorkPackages()
    {
        return $this->workPackages;
    }

    /**
     * @param Measure $measure
     *
     * @return Media
     */
    public function addMeasure(Measure $measure)
    {
        $this->measures[] = $measure;
        $measure->addMedia($this);

        return $this;
    }

    /**
     * @param Measure $measure
     *
     * @return Media
     */
    public function removeMeasure(Measure $measure)
    {
        $this->measures->removeElement($measure);
        $measure->removeMedia($this);

        return $this;
    }

    /**
     * @return Measure[]|ArrayCollection
     */
    public function getMeasures()
    {
        return $this->measures;
    }

    /**
     * @param MeasureComment $measureComment
     *
     * @return Media
     */
    public function addMeasureComment(MeasureComment $measureComment)
    {
        $this->measureComments[] = $measureComment;
        $measureComment->addMedia($this);

        return $this;
    }

    /**
     * @param MeasureComment $measureComment
     *
     * @return Media
     */
    public function removeMeasureComment(MeasureComment $measureComment)
    {
        $this->measureComments->removeElement($measureComment);
        $measureComment->removeMedia($this);

        return $this;
    }

    /**
     * @return Measure[]|ArrayCollection
     */
    public function getMeasureComments()
    {
        return $this->measureComments;
    }

    /**
     * returns the file name.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("fileName")
     *
     * @return string
     */
    public function getFileName()
    {
        return basename($this->path);
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return (string) $this->originalName;
    }

    /**
     * @param string $originalName
     */
    public function setOriginalName(string $originalName = null)
    {
        $this->originalName = $originalName;
    }

    /**
     * @Serializer\VirtualProperty()
     *
     * @return string
     */
    public function getName()
    {
        if (empty($this->getOriginalName())) {
            return (string) $this->getPath();
        }

        return (string) $this->getOriginalName();
    }

    /**
     * @return bool
     */
    public function hasFileSystem()
    {
        return (bool) $this->getFileSystem();
    }

    /**
     * @param Decision $decision
     *
     * @return Media
     */
    public function addDecision(Decision $decision)
    {
        $this->decisions[] = $decision;
        $decision->addMedia($this);

        return $this;
    }

    /**
     * @param Decision $decision
     *
     * @return Media
     */
    public function removeDecision(Decision $decision)
    {
        $this->decisions->removeElement($decision);
        $decision->removeMedia($this);

        return $this;
    }

    /**
     * @return Decision[]|ArrayCollection
     */
    public function getDecisions()
    {
        return $this->decisions;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime|null $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @param int $ttl
     */
    public function makeAsTemporary(int $ttl = self::DEFAULT_EXPIRE_DELTA)
    {
        $expiresAt = new \DateTime();
        $expiresAt->setTimestamp(time() + $ttl);

        $this->expiresAt = $expiresAt;
    }

    public function makeAsPermanent()
    {
        $this->expiresAt = null;
    }

    /**
     * Get real path.
     *
     * @return string
     */
    public function getRealPath()
    {
        return sprintf('%s/%s', $this->getFileSystem()->getConfig()['path'], $this->getPath());
    }
}
