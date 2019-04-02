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

use Jitamin\ExternalLink\FileLink;

class FileLinkTest extends Base
{
    public function testGetTitleFromUrlWithUnixPath()
    {
        $url = 'file:///tmp/test.txt';

        $link = new FileLink($this->container);
        $link->setUrl($url);
        $this->assertEquals($url, $link->getUrl());
        $this->assertEquals('test.txt', $link->getTitle());
    }

    public function testGetTitleFromUrlWithWindowsPath()
    {
        $url = 'file:///c:\temp\test.txt';

        $link = new FileLink($this->container);
        $link->setUrl($url);
        $this->assertEquals($url, $link->getUrl());
        $this->assertEquals('test.txt', $link->getTitle());
    }
}
