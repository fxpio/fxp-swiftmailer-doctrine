<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Tests\Fixtures\Repository;

use Doctrine\ORM\EntityRepository;
use Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface;
use Fxp\Component\SwiftmailerDoctrine\Model\Repository\Traits\SpoolEmailRepositoryTrait;

/**
 * Mock of Spool Email Repository.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class SpoolEmailRepository extends EntityRepository implements SpoolEmailRepositoryInterface
{
    use SpoolEmailRepositoryTrait;
}
