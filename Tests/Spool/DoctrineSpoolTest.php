<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\SwiftmailerDoctrine\Tests\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;
use Fxp\Component\SwiftmailerDoctrine\Entity\SpoolEmail;
use Fxp\Component\SwiftmailerDoctrine\Spool\DoctrineSpool;
use Fxp\Component\SwiftmailerDoctrine\SpoolEmailStatus;
use PHPUnit\Framework\TestCase;

/**
 * Doctrine ORM Spool Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DoctrineSpoolTest extends TestCase
{
    /**
     * @expectedException \Fxp\Component\SwiftmailerDoctrine\Exception\InvalidArgumentException
     * @expectedExceptionMessage The "stdClass" class does not extend "Fxp\Component\SwiftmailerDoctrine\Model\SpoolEmailInterface
     */
    public function testInvalidClass()
    {
        /* @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();

        new DoctrineSpool($registry, 'stdClass');
    }

    /**
     * @expectedException \Fxp\Component\SwiftmailerDoctrine\Exception\InvalidArgumentException
     * @expectedExceptionMessage The repository of "Fxp\Component\SwiftmailerDoctrine\Entity\SpoolEmail" must be an instance of "Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface"
     */
    public function testInvalidRepository()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\ObjectRepository')->getMock();
        $manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $manager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        /* @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();
        $registry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($manager))
        ;

        new DoctrineSpool($registry, 'Fxp\Component\SwiftmailerDoctrine\Entity\SpoolEmail');
    }

    public function testBasic()
    {
        $spool = $this->createSpool();

        $this->assertFalse($spool->isStarted());
        $spool->start();

        $this->assertTrue($spool->isStarted());
        $spool->stop();
        $this->assertFalse($spool->isStarted());
    }

    public function testQueueMessage()
    {
        /* @var \Swift_Mime_SimpleMessage $message */
        $message = $this->getMockBuilder('Swift_Mime_SimpleMessage')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->assertTrue($this->createSpool()->queueMessage($message));
    }

    public function testFlushQueueEmpty()
    {
        $failedRecipients = [];
        /* @var \Swift_Transport|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMockBuilder('Swift_Transport')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->assertEquals(0, $this->createSpool()->flushQueue($transport, $failedRecipients));
        $this->assertCount(0, $failedRecipients);
    }

    public function testFlushQueueFailed()
    {
        $failedRecipients = [];
        /* @var \Swift_Transport|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMockBuilder('Swift_Transport')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        /* @var \Swift_Mime_SimpleMessage $message */
        $message = $this->getMockBuilder('Swift_Mime_SimpleMessage')->disableOriginalConstructor()->getMock();
        $email = new SpoolEmail($message);

        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $email->getStatus());
        $this->assertEquals(0, $this->createSpool([$email])->flushQueue($transport, $failedRecipients));
        $this->assertCount(0, $failedRecipients);
        $this->assertSame(SpoolEmailStatus::STATUS_FAILED, $email->getStatus());
        $this->assertNull($email->getStatusMessage());
    }

    public function testFlushQueueFailedException()
    {
        $failedRecipients = [];
        /* @var \Swift_Transport|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMockBuilder('Swift_Transport')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $transport->expects($this->any())
            ->method('send')
            ->will($this->throwException(new \Swift_TransportException('Message exception')));

        /* @var \Swift_Mime_SimpleMessage $message */
        $message = $this->getMockBuilder('Swift_Mime_SimpleMessage')->disableOriginalConstructor()->getMock();
        $email = new SpoolEmail($message);

        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $email->getStatus());
        $this->assertEquals(0, $this->createSpool([$email])->flushQueue($transport, $failedRecipients));
        $this->assertCount(0, $failedRecipients);
        $this->assertSame(SpoolEmailStatus::STATUS_FAILED, $email->getStatus());
        $this->assertSame('Message exception', $email->getStatusMessage());
    }

    public function testFlushQueueSuccess()
    {
        $failedRecipients = [];
        /* @var \Swift_Transport|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMockBuilder('Swift_Transport')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $transport->expects($this->any())
            ->method('send')
            ->will($this->returnValue(1));

        /* @var \Swift_Mime_SimpleMessage $message */
        $message = $this->getMockBuilder('Swift_Mime_SimpleMessage')->disableOriginalConstructor()->getMock();
        $email = new SpoolEmail($message);

        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $email->getStatus());
        $this->assertEquals(1, $this->createSpool([$email])->flushQueue($transport, $failedRecipients));
        $this->assertCount(0, $failedRecipients);
        $this->assertSame(SpoolEmailStatus::STATUS_SUCCESS, $email->getStatus());
        $this->assertNull($email->getStatusMessage());
    }

    public function testFlushQueueTimeout()
    {
        $failedRecipients = [];
        /* @var \Swift_Transport|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMockBuilder('Swift_Transport')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $transport->expects($this->any())
            ->method('send')
            ->will($this->returnCallback(function () {
                sleep(1);

                return 1;
            }));

        /* @var \Swift_Mime_SimpleMessage $message1 */
        $message1 = $this->getMockBuilder('Swift_Mime_SimpleMessage')->disableOriginalConstructor()->getMock();
        $email1 = new SpoolEmail($message1);
        /* @var \Swift_Mime_SimpleMessage $message2 */
        $message2 = $this->getMockBuilder('Swift_Mime_SimpleMessage')->disableOriginalConstructor()->getMock();
        $email2 = new SpoolEmail($message2);

        $spool = $this->createSpool([$email1, $email2]);
        $spool->setTimeLimit(1);

        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $email1->getStatus());
        $this->assertSame(SpoolEmailStatus::STATUS_WAITING, $email2->getStatus());
        $this->assertEquals(1, $spool->flushQueue($transport, $failedRecipients));
        //$spool->flushQueue($transport, $failedRecipients);
        $this->assertCount(0, $failedRecipients);
        $this->assertSame(SpoolEmailStatus::STATUS_SUCCESS, $email1->getStatus());
        $this->assertNull($email1->getStatusMessage());
        $this->assertSame(SpoolEmailStatus::STATUS_FAILED, $email2->getStatus());
        $this->assertSame('The time limit of execution is exceeded', $email2->getStatusMessage());
    }

    public function testRecover()
    {
        $spool = $this->createSpool();
        $spool->recover(900);

        $this->assertFalse($spool->isStarted());
    }

    /**
     * @param array $emailsToSend
     *
     * @return DoctrineSpool
     */
    protected function createSpool($emailsToSend = [])
    {
        $repo = $this->getMockBuilder('Fxp\Component\SwiftmailerDoctrine\Model\Repository\SpoolEmailRepositoryInterface')->getMock();
        $repo->expects($this->any())
            ->method('findEmailsToSend')
            ->will($this->returnValue($emailsToSend));

        $manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $manager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        /* @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();
        $registry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($manager))
        ;

        return new DoctrineSpool($registry, 'Fxp\Component\SwiftmailerDoctrine\Entity\SpoolEmail');
    }
}
