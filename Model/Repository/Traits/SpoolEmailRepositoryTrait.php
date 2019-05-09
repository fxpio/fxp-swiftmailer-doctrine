<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Model\Repository\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface;
use Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus;

/**
 * Trait of Spool email repository.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @method QueryBuilder           createQueryBuilder($alias, $indexBy = null)
 * @method EntityManagerInterface getEntityManager()
 * @method string                 getClassName()
 */
trait SpoolEmailRepositoryTrait
{
    /**
     * @see SpoolEmailRepositoryInterface::findEmailsToSend()
     *
     * @param null|mixed $limit
     *
     * @throws
     *
     * @return mixed
     */
    public function findEmailsToSend(?int $limit = null): array
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
     * @see SpoolEmailRepositoryInterface::recover()
     *
     * @param mixed $timeout
     *
     * @throws
     */
    public function recover(int $timeout = 900): void
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
