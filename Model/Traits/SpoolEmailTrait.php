<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Fxp\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface;
use Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus;

/**
 * Trait of Spool email model.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
trait SpoolEmailTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @var null|\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $sentAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $status = SpoolEmailStatus::STATUS_WAITING;

    /**
     * @var null|string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $statusMessage;

    /**
     * @see SpoolEmailInterface::setMessage()
     */
    public function setMessage(\Swift_Mime_SimpleMessage $message): SpoolEmailInterface
    {
        $this->message = serialize($message);

        return $this;
    }

    /**
     * @see SpoolEmailInterface::getMessage()
     */
    public function getMessage(): ?\Swift_Mime_SimpleMessage
    {
        return null !== $this->message ? unserialize($this->message) : null;
    }

    /**
     * @see SpoolEmailInterface::getSentAt()
     */
    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    /**
     * @see SpoolEmailInterface::setSentAt()
     *
     * @param mixed $sentAt
     *
     * @return SpoolEmailInterface
     */
    public function setSentAt(?\DateTime $sentAt): SpoolEmailInterface
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * @see SpoolEmailInterface::setStatus()
     *
     * @param mixed $status
     *
     * @return SpoolEmailInterface
     */
    public function setStatus(int $status): SpoolEmailInterface
    {
        $this->status = $status;
        $this->setStatusMessage(null);

        return $this;
    }

    /**
     * @see SpoolEmailInterface::getStatus()
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @see SpoolEmailInterface::setStatusMessage()
     *
     * @param null|string $message
     *
     * @return SpoolEmailInterface
     */
    public function setStatusMessage(?string $message): SpoolEmailInterface
    {
        $this->statusMessage = $message;

        return $this;
    }

    /**
     * @see SpoolEmailInterface::getStatusMessage()
     */
    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }
}
