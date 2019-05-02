<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Tests\Entity;

use Fxp\Component\SwiftmailerDoctrine\Entity\SpoolEmail;
use Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus;
use PHPUnit\Framework\TestCase;

/**
 * SpoolEmail Entity Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class SpoolEmailTest extends TestCase
{
    public function testDefaultValue(): void
    {
        $message = $this->createSwiftMessage();
        $sp = new SpoolEmail($message);

        $this->assertNull($sp->getId());
        $this->assertEquals($message, $sp->getMessage());
        $this->assertNull($sp->getSentAt());
        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $sp->getStatus());
        $this->assertNull($sp->getStatusMessage());
    }

    public function testEdition(): void
    {
        $message = $this->createSwiftMessage();
        $sp = new SpoolEmail($message);

        $sp->setMessage($message);
        $this->assertEquals($message, $sp->getMessage());

        $date = new \DateTime();
        $sp->setSentAt($date);
        $this->assertSame($date, $sp->getSentAt());
        $sp->setSentAt(null);
        $this->assertNull($sp->getSentAt());

        $sp->setStatus(SpoolEmailStatus::STATUS_SENDING);
        $this->assertSame(SpoolEmailStatus::STATUS_SENDING, $sp->getStatus());

        $statusMsg = 'Status message';
        $sp->setStatusMessage($statusMsg);
        $this->assertSame($statusMsg, $sp->getStatusMessage());
        $sp->setStatusMessage(null);
        $this->assertNull($sp->getStatusMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Swift_Mime_SimpleMessage
     */
    protected function createSwiftMessage()
    {
        return $this->getMockBuilder('Swift_Mime_SimpleMessage')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
