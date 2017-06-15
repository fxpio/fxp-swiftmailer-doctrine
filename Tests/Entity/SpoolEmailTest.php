<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\SwiftmailerDoctrine\Tests\Entity;

use Sonatra\Component\SwiftmailerDoctrine\Entity\SpoolEmail;
use Sonatra\Component\SwiftmailerDoctrine\SpoolEmailStatus;

/**
 * SpoolEmail Entity Tests.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SpoolEmailTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultValue()
    {
        $message = $this->createSwiftMessage();
        $sp = new SpoolEmail($message);

        $this->assertNull($sp->getId());
        $this->assertEquals($message, $sp->getMessage());
        $this->assertNull($sp->getSentAt());
        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $sp->getStatus());
        $this->assertNull($sp->getStatusMessage());
    }

    public function testEdition()
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
     * @return \Swift_Mime_SimpleMessage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSwiftMessage()
    {
        $message = $this->getMockBuilder('Swift_Mime_SimpleMessage')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $message;
    }
}
