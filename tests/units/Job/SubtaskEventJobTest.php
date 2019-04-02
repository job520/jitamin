<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Jitamin\Bus\Job\SubtaskEventJob;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\SubtaskModel;
use Jitamin\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class SubtaskEventJobTest extends Base
{
    public function testJobParams()
    {
        $subtaskEventJob = new SubtaskEventJob($this->container);
        $subtaskEventJob->withParams(123, 'foobar', ['k' => 'v']);

        $this->assertSame([123, 'foobar', ['k' => 'v']], $subtaskEventJob->getJobParams());
    }

    public function testWithMissingSubtask()
    {
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_CREATE, function () {
        });

        $subtaskEventJob = new SubtaskEventJob($this->container);
        $subtaskEventJob->execute(42, SubtaskModel::EVENT_CREATE);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);
    }

    public function testTriggerEvents()
    {
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_CREATE, function () {
        });
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_UPDATE, function () {
        });
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_DELETE, function () {
        });

        $subtaskModel = new SubtaskModel($this->container);
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['title' => 'test', 'project_id' => 1]));
        $this->assertEquals(1, $subtaskModel->create(['task_id' => 1, 'title' => 'before']));
        $this->assertTrue($subtaskModel->update(['id' => 1, 'task_id' => 1, 'title' => 'after']));
        $this->assertTrue($subtaskModel->remove(1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(SubtaskModel::EVENT_CREATE.'.closure', $called);
        $this->assertArrayHasKey(SubtaskModel::EVENT_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(SubtaskModel::EVENT_DELETE.'.closure', $called);
    }
}
