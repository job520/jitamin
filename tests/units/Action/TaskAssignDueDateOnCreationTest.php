<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Jitamin\Action\TaskAssignDueDateOnCreation;
use Jitamin\Bus\EventBuilder\TaskEventBuilder;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskFinderModel;
use Jitamin\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class TaskAssignDueDateOnCreationTest extends Base
{
    public function testAction()
    {
        $projectModel = new ProjectModel($this->container);
        $taskModel = new TaskModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['project_id' => 1, 'title' => 'test']));

        $event = TaskEventBuilder::getInstance($this->container)
            ->withTaskId(1)
            ->buildEvent();

        $action = new TaskAssignDueDateOnCreation($this->container);
        $action->setProjectId(1);
        $action->setParam('duration', 4);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CREATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(date('Y-m-d', strtotime('+4days')), date('Y-m-d', $task['date_due']));
    }
}
