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

use Jitamin\Bus\EventBuilder\SubtaskEventBuilder;

/**
 * Class SubtaskEventJob.
 */
class SubtaskEventJob extends BaseJob
{
    /**
     * Set job params.
     *
     * @param int    $subtaskId
     * @param string $eventName
     * @param array  $values
     *
     * @return $this
     */
    public function withParams($subtaskId, $eventName, array $values = [])
    {
        $this->jobParams = [$subtaskId, $eventName, $values];

        return $this;
    }

    /**
     * Execute job.
     *
     * @param int    $subtaskId
     * @param string $eventName
     * @param array  $values
     *
     * @return $this
     */
    public function execute($subtaskId, $eventName, array $values = [])
    {
        $event = SubtaskEventBuilder::getInstance($this->container)
            ->withSubtaskId($subtaskId)
            ->withValues($values)
            ->buildEvent();

        if ($event !== null) {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}
