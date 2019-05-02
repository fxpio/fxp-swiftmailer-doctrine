<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface;
use Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus;

/**
 * Spool email entity repository.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class SpoolEmailRepository extends EntityRepository implements SpoolEmailRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findEmailsToSend($limit = null)
    {
        $qb = $this->createQueryBuilder('se')
            ->where('se.status = :status AND (se.sentAt IS NULL OR se.sentAt <= :sentAt)')
            ->orderBy('se.sentAt', 'ASC')
            ->setParameter('status', SpoolEmailStatus::STATUS_WAITING)
            ->setParameter('sentAt', new \DateTime())
        ;

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function recover($timeout = 900): void
    {
        $timeoutDate = new \DateTime();
        $timeoutDate->modify(sprintf('-%s seconds', $timeout));

        $str = sprintf('UPDATE %s se SET se.sentAt = null, se.status = :waitStatus, se.statusMessage = null WHERE se.status = :failedStatus AND se.sentAt <= :timeoutDate', $this->getClassName());
        $query = $this->getEntityManager()->createQuery($str)
            ->setParameter('waitStatus', SpoolEmailStatus::STATUS_WAITING)
            ->setParameter('failedStatus', SpoolEmailStatus::STATUS_FAILED)
            ->setParameter('timeoutDate', $timeoutDate)
        ;

        $query->execute();
    }
}
