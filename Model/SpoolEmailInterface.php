<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Model;

/**
 * Spool email interface.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface SpoolEmailInterface
{
    /**
     * Set the swiftmailer message.
     *
     * @param \Swift_Mime_SimpleMessage $message
     *
     * @return self
     */
    public function setMessage(\Swift_Mime_SimpleMessage $message);

    /**
     * Get the swiftmailer message.
     *
     * @return \Swift_Mime_SimpleMessage
     */
    public function getMessage();

    /**
     * Set the sent date.
     *
     * @param null|\DateTime $sentAt The sent date
     *
     * @return self
     */
    public function setSentAt($sentAt);

    /**
     * Get the sent date.
     *
     * @return null|\DateTime
     */
    public function getSentAt();

    /**
     * Set the status.
     *
     * Defined by constants in Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus.
     *
     * @param int $status The status
     *
     * @return self
     */
    public function setStatus($status);

    /**
     * Get the status.
     *
     * Defined by constants in Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set the status message.
     *
     * @param null|string $message The status message
     *
     * @return self
     */
    public function setStatusMessage($message);

    /**
     * Get the status message.
     *
     * @return null|string
     */
    public function getStatusMessage();
}
