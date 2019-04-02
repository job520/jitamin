<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Bus\Job;

/**
 * Class EmailJob.
 */
class EmailJob extends BaseJob
{
    /**
     * Set job parameters.
     *
     * @param string $email
     * @param string $name
     * @param string $subject
     * @param string $html
     * @param string $author
     *
     * @return $this
     */
    public function withParams($email, $name, $subject, $html, $author)
    {
        $this->jobParams = [$email, $name, $subject, $html, $author];

        return $this;
    }

    /**
     * Execute job.
     *
     * @param string $email
     * @param string $name
     * @param string $subject
     * @param string $html
     * @param string $author
     */
    public function execute($email, $name, $subject, $html, $author)
    {
        $transport = $this->helper->mail->getMailTransport();
        $this->logger->debug(__METHOD__.' Sending email to: '.$email.' using transport: '.$transport);
        $startTime = microtime(true);

        $this->emailClient
            ->getTransport($transport)
            ->sendEmail($email, $name, $subject, $html, $author);

        if (DEBUG) {
            $this->logger->debug('Email sent in '.round(microtime(true) - $startTime, 6).' seconds');
        }
    }
}
