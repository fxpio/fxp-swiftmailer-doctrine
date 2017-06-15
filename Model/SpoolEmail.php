<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\SwiftmailerDoctrine\Model;

use Sonatra\Component\SwiftmailerDoctrine\SpoolEmailStatus;

/**
 * Spool email model.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class SpoolEmail implements SpoolEmailInterface
{
    /**
     * @var int|string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \DateTime|null
     */
    protected $sentAt;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $statusMessage;

    /**
     * Constructor.
     *
     * @param \Swift_Mime_SimpleMessage $message The swift message
     */
    public function __construct(\Swift_Mime_SimpleMessage $message)
    {
        $this->setMessage($message);
        $this->status = SpoolEmailStatus::STATUS_WAITING;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage(\Swift_Mime_SimpleMessage $message)
    {
        $this->message = base64_encode(serialize($message));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return unserialize(base64_decode($this->message));
    }

    /**
     * {@inheritdoc}
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->setStatusMessage(null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusMessage($message)
    {
        $this->statusMessage = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }
}
