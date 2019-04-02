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

use Jitamin\Action\CommentCreationMoveTaskColumn;
use Jitamin\Bus\Event\TaskEvent;
use Jitamin\Model\CommentModel;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskModel;

class CommentCreationMoveTaskColumnTest extends Base
{
    public function testSuccess()
    {
        $this->container['sessionStorage']->user = ['id' => 1];

        $projectModel = new ProjectModel($this->container);
        $commentModel = new CommentModel($this->container);
        $taskModel = new TaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['project_id' => 1, 'title' => 'test']));

        $event = new TaskEvent(['task' => ['project_id' => 1, 'column_id' => 2], 'task_id' => 1]);

        $action = new CommentCreationMoveTaskColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_MOVE_COLUMN));

        $comment = $commentModel->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('Moved to column Ready', $comment['comment']);
    }

    public function testWithUserNotLogged()
    {
        $projectModel = new ProjectModel($this->container);
        $taskModel = new TaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['project_id' => 1, 'title' => 'test']));

        $event = new TaskEvent(['task' => ['project_id' => 1, 'column_id' => 3], 'task_id' => 1]);

        $action = new CommentCreationMoveTaskColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_MOVE_COLUMN));
    }
}
