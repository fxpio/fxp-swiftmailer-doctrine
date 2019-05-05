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
    public function setMessage(\Swift_Mime_SimpleMessage $message)
    {
        $this->message = serialize($message);

        return $this;
    }

    /**
     * @see SpoolEmailInterface::getMessage()
     */
    public function getMessage()
    {
        return null !== $this->message ? unserialize($this->message) : null;
    }

    /**
     * @see SpoolEmailInterface::getSentAt()
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @see SpoolEmailInterface::setSentAt()
     *
     * @param mixed $sentAt
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * @see SpoolEmailInterface::setStatus()
     *
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->setStatusMessage(null);

        return $this;
    }

    /**
     * @see SpoolEmailInterface::getStatus()
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @see SpoolEmailInterface::setStatusMessage()
     *
     * @param mixed $message
     */
    public function setStatusMessage($message)
    {
        $this->statusMessage = $message;

        return $this;
    }

    /**
     * @see SpoolEmailInterface::getStatusMessage()
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }
}
