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

use Jitamin\Action\TaskAssignColorPriority;
use Jitamin\Bus\Event\TaskEvent;
use Jitamin\Model\CategoryModel;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskFinderModel;
use Jitamin\Model\TaskModel;

class TaskAssignColorPriorityTest extends Base
{
    public function testChangeColor()
    {
        $categoryModel = new CategoryModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskModel = new TaskModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['project_id' => 1, 'title' => 'test']));
        $this->assertEquals(1, $categoryModel->create(['name' => 'c1', 'project_id' => 1]));

        $event = new TaskEvent([
            'task_id' => 1,
            'task'    => [
                'project_id' => 1,
                'priority'   => 1,
            ],
        ]);

        $action = new TaskAssignColorPriority($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('priority', 1);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('red', $task['color_id']);
    }

    public function testWithWrongPriority()
    {
        $projectModel = new ProjectModel($this->container);
        $taskModel = new TaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['project_id' => 1, 'title' => 'test']));

        $event = new TaskEvent([
            'task_id' => 1,
            'task'    => [
                'project_id' => 1,
                'priority'   => 2,
            ],
        ]);

        $action = new TaskAssignColorPriority($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('priority', 1);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_CREATE_UPDATE));
    }
}
