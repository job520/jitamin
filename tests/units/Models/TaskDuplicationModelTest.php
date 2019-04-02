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

use Jitamin\Model\CategoryModel;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskDuplicationModel;
use Jitamin\Model\TaskFinderModel;
use Jitamin\Model\TaskModel;
use Jitamin\Model\TaskTagModel;

class TaskDuplicationModelTest extends Base
{
    public function testThatDuplicateDefineCreator()
    {
        $taskDuplicationModel = new TaskDuplicationModel($this->container);
        $taskModel = new TaskModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create(['title' => 'test', 'project_id' => 1]));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(0, $task['creator_id']);

        $this->container['sessionStorage']->user = ['id' => 1];

        // We duplicate our task
        $this->assertEquals(2, $taskDuplicationModel->duplicate(1));

        // Check the values of the duplicated task
        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['creator_id']);
    }

    public function testDuplicateSameProject()
    {
        $taskDuplicationModel = new TaskDuplicationModel($this->container);
        $taskModel = new TaskModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        // We create a task and a project
        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));

        // Some categories
        $this->assertNotFalse($categoryModel->create(['name' => 'Category #1', 'project_id' => 1]));
        $this->assertNotFalse($categoryModel->create(['name' => 'Category #2', 'project_id' => 1]));
        $this->assertTrue($categoryModel->exists(1));
        $this->assertTrue($categoryModel->exists(2));

        $this->assertEquals(1, $taskModel->create([
            'title'       => 'test',
            'project_id'  => 1,
            'column_id'   => 3,
            'owner_id'    => 1,
            'category_id' => 2,
            'time_spent'  => 4.4,
        ]));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(4.4, $task['time_spent']);

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, function () {
        });
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE, function () {
        });

        // We duplicate our task
        $this->assertEquals(2, $taskDuplicationModel->duplicate(1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE.'.closure', $called);

        // Check the values of the duplicated task
        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(0, $task['time_spent']);
    }

    public function testDuplicateSameProjectWithTags()
    {
        $taskDuplicationModel = new TaskDuplicationModel($this->container);
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskTagModel = new TaskTagModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create([
            'title'      => 'test',
            'project_id' => 1,
            'tags'       => ['T1', 'T2'],
        ]));

        $this->assertEquals(2, $taskDuplicationModel->duplicate(1));

        $tags = $taskTagModel->getList(2);
        $this->assertCount(2, $tags);
        $this->assertArrayHasKey(1, $tags);
        $this->assertArrayHasKey(2, $tags);
    }

    public function testDuplicateSameProjectWithPriority()
    {
        $taskDuplicationModel = new TaskDuplicationModel($this->container);
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskModel->create([
            'title'      => 'test',
            'project_id' => 1,
            'priority'   => 2,
        ]));

        $this->assertEquals(2, $taskDuplicationModel->duplicate(1));

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['priority']);
    }
}
