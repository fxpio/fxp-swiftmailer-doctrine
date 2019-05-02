<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Spool;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Fxp\Component\SwiftmailerDoctrine\Exception\InvalidArgumentException;
use Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface;
use Fxp\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface;
use Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus;
use Swift_Transport;

/**
 * Doctrine Spool for Swiftmailer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DoctrineSpool extends \Swift_ConfigurableSpool
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var SpoolEmailRepositoryInterface
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The doctrine registry
     * @param string          $class    The class name of spool email entity
     *
     * @throws InvalidArgumentException When the class has not the interface Fxp\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface
     * @throws InvalidArgumentException When the repository is not an instance of Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface
     */
    public function __construct(ManagerRegistry $registry, $class)
    {
        $validClass = 'Fxp\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface';
        $ref = new \ReflectionClass($class);

        if (!\in_array($validClass, $ref->getInterfaceNames(), true)) {
            $msg = sprintf('The "%s" class does not extend "%s"', $class, $validClass);

            throw new InvalidArgumentException($msg);
        }

        $this->om = $registry->getManagerForClass($class);
        $this->repo = $this->om->getRepository($class);
        $this->class = $class;

        if (!$this->repo instanceof SpoolEmailRepositoryInterface) {
            $msg = sprintf('The repository of "%s" must be an instance of "%s"', $class, 'Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface');

            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        $this->started = true;
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): void
    {
        $this->started = false;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function queueMessage(\Swift_Mime_SimpleMessage $message)
    {
        $entity = new $this->class($message);
        $this->om->persist($entity);
        $this->om->flush();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        if (!$transport->isStarted()) {
            $transport->start();
        }

        $emails = $this->repo->findEmailsToSend($this->getMessageLimit());

        return \count($emails) > 0
            ? $this->sendEmails($transport, $failedRecipients, $emails)
            : 0;
    }

    /**
     * Execute a recovery if for any reason a process is sending for too long.
     *
     * @param int $timeout In second, Defaults is for very slow smtp responses
     */
    public function recover($timeout = 900): void
    {
        $this->repo->recover($timeout);
    }

    /**
     * Send the emails message.
     *
     * @param \Swift_Transport      $transport        The swift transport
     * @param null|string[]         $failedRecipients The failed recipients
     * @param SpoolEmailInterface[] $emails           The spool emails
     *
     * @return int The count of sent emails
     */
    protected function sendEmails(Swift_Transport $transport, &$failedRecipients, array $emails)
    {
        $count = 0;
        $time = time();
        $emails = $this->prepareEmails($emails);
        $skip = false;

        foreach ($emails as $email) {
            if ($skip) {
                $email->setStatus(SpoolEmailStatus::STATUS_FAILED);
                $email->setStatusMessage('The time limit of execution is exceeded');

                continue;
            }

            $count += $this->sendEmail($transport, $email, $failedRecipients);
            $this->flushEmail($email);

            if ($this->getTimeLimit() && (time() - $time) >= $this->getTimeLimit()) {
                $skip = true;
            }
        }

        return $count;
    }

    /**
     * Prepare the spool emails.
     *
     * @param SpoolEmailInterface[] $emails The spool emails
     *
     * @return SpoolEmailInterface[]
     */
    protected function prepareEmails(array $emails)
    {
        foreach ($emails as $email) {
            $email->setStatus(SpoolEmailStatus::STATUS_SENDING);
            $email->setSentAt(null);
            $this->om->persist($email);
        }

        $this->om->flush();
        reset($emails);

        return $emails;
    }

    /**
     * Send the spool email.
     *
     * @param Swift_Transport     $transport        The swiftmailer transport
     * @param SpoolEmailInterface $email            The spool email
     * @param null|string[]       $failedRecipients The failed recipients
     *
     * @return int The count
     */
    protected function sendEmail(Swift_Transport $transport, SpoolEmailInterface $email, &$failedRecipients)
    {
        $count = 0;

        try {
            if ($transport->send($email->getMessage(), $failedRecipients)) {
                $email->setStatus(SpoolEmailStatus::STATUS_SUCCESS);
                ++$count;
            } else {
                $email->setStatus(SpoolEmailStatus::STATUS_FAILED);
            }
        } catch (\Swift_TransportException $e) {
            $email->setStatus(SpoolEmailStatus::STATUS_FAILED);
            $email->setStatusMessage($e->getMessage());
        }

        return $count;
    }

    /**
     * Update and flush the spool email.
     *
     * @param SpoolEmailInterface $email The spool email
     */
    protected function flushEmail(SpoolEmailInterface $email): void
    {
        $email->setSentAt(new \DateTime());
        $this->om->persist($email);
        $this->om->flush();

        if (SpoolEmailStatus::STATUS_SUCCESS === $email->getStatus()) {
            $this->om->remove($email);
            $this->om->flush();
        }

        $this->om->detach($email);
    }
}
