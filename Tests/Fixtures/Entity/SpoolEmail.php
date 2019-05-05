<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Tests\Fixtures\Entity;

use Fxp\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface;
use Fxp\Component\SwiftmailerDoctrine\Model\Traits\SpoolEmailTrait;

/**
 * Mock of Spool Email Entity.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class SpoolEmail implements SpoolEmailInterface
{
    use SpoolEmailTrait;

    private $id;

    public function getId()
    {
        return $this->id;
    }
}
