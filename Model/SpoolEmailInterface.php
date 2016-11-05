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

/**
 * Spool email interface.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
interface SpoolEmailInterface
{
    /**
     * Get the id.
     *
     * @return int|string|null
     */
    public function getId();

    /**
     * Set the swiftmailer message.
     *
     * @param \Swift_Mime_Message $message
     *
     * @return self
     */
    public function setMessage(\Swift_Mime_Message $message);

    /**
     * Get the swiftmailer message.
     *
     * @return \Swift_Mime_Message
     */
    public function getMessage();

    /**
     * Set the sent date.
     *
     * @param \DateTime|null $sentAt The sent date
     *
     * @return self
     */
    public function setSentAt($sentAt);

    /**
     * Get the sent date.
     *
     * @return \DateTime|null
     */
    public function getSentAt();

    /**
     * Set the status.
     *
     * Defined by constants in Sonatra\Component\SwiftmailerDoctrine\SpoolEmailStatus.
     *
     * @param int $status The status
     *
     * @return self
     */
    public function setStatus($status);

    /**
     * Get the status.
     *
     * Defined by constants in Sonatra\Component\SwiftmailerDoctrine\SpoolEmailStatus.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set the status message.
     *
     * @param string|null $message The status message
     *
     * @return self
     */
    public function setStatusMessage($message);

    /**
     * Get the status message.
     *
     * @return string|null
     */
    public function getStatusMessage();
}
