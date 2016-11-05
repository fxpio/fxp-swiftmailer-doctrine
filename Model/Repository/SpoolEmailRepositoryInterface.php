<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\SwiftmailerDoctrine\Model\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Sonatra\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface;

/**
 * Spool email repository interface.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
interface SpoolEmailRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * Find the emails to send.
     *
     * @param int|null $limit The limit
     *
     * @return SpoolEmailInterface[]
     */
    public function findEmailsToSend($limit = null);

    /**
     * Execute a recovery if for any reason a process is sending for too long.
     *
     * @param int $timeout In second, Defaults is for very slow smtp responses
     */
    public function recover($timeout = 900);
}
