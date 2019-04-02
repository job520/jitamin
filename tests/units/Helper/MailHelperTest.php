<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../Base.php';

use Jitamin\Helper\MailHelper;

class MailHelperTest extends Base
{
    public function testMailboxHash()
    {
        $helper = new MailHelper($this->container);
        $this->assertEquals('test1', $helper->getMailboxHash('a+test1@localhost'));
        $this->assertEquals('', $helper->getMailboxHash('test1@localhost'));
        $this->assertEquals('', $helper->getMailboxHash('test1'));
    }

    public function testFilterSubject()
    {
        $helper = new MailHelper($this->container);
        $this->assertEquals('Test', $helper->filterSubject('Test'));
        $this->assertEquals('Test', $helper->filterSubject('RE: Test'));
        $this->assertEquals('Test', $helper->filterSubject('FW: Test'));
    }

    public function testGetSenderAddress()
    {
        $helper = new MailHelper($this->container);
        $this->assertEquals('notifications@jitamin.local', $helper->getMailSenderAddress());

        $this->container['settingModel']->save(['mail_sender_address' => 'me@here']);
        $this->container['memoryCache']->flush();

        $this->assertEquals('me@here', $helper->getMailSenderAddress());
    }

    public function testGetTransport()
    {
        $helper = new MailHelper($this->container);
        $this->assertEquals(MAIL_TRANSPORT, $helper->getMailTransport());

        $this->container['settingModel']->save(['mail_transport' => 'smtp']);
        $this->container['memoryCache']->flush();

        $this->assertEquals('smtp', $helper->getMailTransport());
    }
}
