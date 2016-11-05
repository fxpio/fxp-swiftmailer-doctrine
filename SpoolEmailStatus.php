<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\SwiftmailerDoctrine;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class SpoolEmailStatus
{
    /**
     * The SpoolEmailStatus::STATUS_FAILED is used in SpoolEmailInterface::getStatus().
     */
    const STATUS_FAILED = -1;

    /**
     * The SpoolEmailStatus::STATUS_WAITING is used in SpoolEmailInterface::getStatus().
     */
    const STATUS_WAITING = 0;

    /**
     * The SpoolEmailStatus::STATUS_SENDING is used in SpoolEmailInterface::getStatus().
     */
    const STATUS_SENDING = 1;

    /**
     * The SpoolEmailStatus::STATUS_SUCCESS is used in SpoolEmailInterface::getStatus().
     */
    const STATUS_SUCCESS = 2;
}
