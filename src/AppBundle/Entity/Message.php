<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule.
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Project|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", inversedBy="messages", cascade={"remove"})
     * @ORM\JoinColumn(name="project_id", nullable=false)
     */
    private $project;

    /**
     * @var Chat|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ChatRoom", inversedBy="messages", cascade={"remove"})
     * @ORM\JoinColumn(name="chat_room_id")
     */
    private $chatRoom;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="from_id", nullable=false)
     */
    private $from;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="to_id")
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="read_at", type="datetime", nullable=true)
     */
    private $readAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set body.
     *
     * @param string $body
     *
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Message
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set readAt.
     *
     * @param \DateTime $readAt
     *
     * @return Message
     */
    public function setReadAt(\DateTime $readAt = null)
    {
        $this->readAt = $readAt;

        return $this;
    }

    /**
     * Get readAt.
     *
     * @return \DateTime
     */
    public function getReadAt()
    {
        return $this->readAt;
    }

    /**
     * Set project.
     *
     * @param Project $project
     *
     * @return Message
     */
    public function setProject(Project $project)
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
     * Set chatRoom.
     *
     * @param ChatRoom $chatRoom
     *
     * @return Message
     */
    public function setChatRoom(ChatRoom $chatRoom = null)
    {
        $this->chatRoom = $chatRoom;

        return $this;
    }

    /**
     * Get chatRoom.
     *
     * @return ChatRoom
     */
    public function getChatRoom()
    {
        return $this->chatRoom;
    }

    /**
     * Set from.
     *
     * @param User $from
     *
     * @return Message
     */
    public function setFrom(User $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from.
     *
     * @return User
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to.
     *
     * @param User $to
     *
     * @return Message
     */
    public function setTo(User $to = null)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to.
     *
     * @return User
     */
    public function getTo()
    {
        return $this->to;
    }
}
