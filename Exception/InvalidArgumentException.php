<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\SwiftmailerDoctrineBundle\Exception;

/**
 * Base InvalidArgumentException for the swiftmailer doctrine bundle.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
