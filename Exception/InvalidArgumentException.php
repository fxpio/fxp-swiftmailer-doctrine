<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Exception;

/**
 * Base InvalidArgumentException for the swiftmailer doctrine bundle.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
