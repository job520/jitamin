<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Jitamin\Filter\TaskMovedDateFilter;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\TaskFinderModel;
use Jitamin\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class TaskMovedDateFilterTest extends Base
{
    public function test()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskModel = new TaskModel($this->container);
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $taskModel->create(['title' => 'Test1', 'project_id' => 1]));
        $this->assertTrue($taskModel->update(['id' => 1, 'date_moved' => time()]));
        $this->assertEquals(2, $taskModel->create(['title' => 'Test2', 'project_id' => 1]));
        $this->assertTrue($taskModel->update(['id' => 2, 'date_moved' => strtotime('-1days')]));
        $this->assertEquals(3, $taskModel->create(['title' => 'Test3', 'project_id' => 1]));
        $this->assertTrue($taskModel->update(['id' => 3, 'date_moved' => strtotime('-3days')]));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskMovedDateFilter('>='.date('Y-m-d', strtotime('-1days')));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $this->assertCount(2, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskMovedDateFilter('<yesterday');
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $this->assertCount(1, $query->findAll());
    }
}
