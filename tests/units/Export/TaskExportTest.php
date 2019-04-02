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

use Jitamin\Export\TaskExport;
use Jitamin\Model\CategoryModel;
use Jitamin\Model\ProjectModel;
use Jitamin\Model\SwimlaneModel;
use Jitamin\Model\TaskModel;

class TaskExportTest extends Base
{
    public function testExport()
    {
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $taskExport = new TaskExport($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'Export Project']));
        $this->assertEquals(1, $swimlaneModel->create(['project_id' => 1, 'name' => 'S1']));
        $this->assertEquals(1, $categoryModel->create(['name' => 'Category #1', 'project_id' => 1]));

        $this->assertEquals(1, $taskModel->create([
            'project_id'     => 1,
            'column_id'      => 2,
            'category_id'    => 1,
            'reference'      => 'REF1',
            'title'          => 'Task 1',
            'time_estimated' => 2.5,
            'time_spent'     => 3,
        ]));

        $this->assertEquals(2, $taskModel->create([
            'project_id'  => 1,
            'swimlane_id' => 1,
            'title'       => 'Task 2',
            'date_due'    => time(),
        ]));

        $report = $taskExport->export(1, date('Y-m-d'), date('Y-m-d'));

        $this->assertCount(3, $report);
        $this->assertCount(22, $report[0]);
        $this->assertEquals('Task Id', $report[0][0]);

        $this->assertEquals(1, $report[1][0]);
        $this->assertEquals(2, $report[2][0]);

        $this->assertEquals('REF1', $report[1][1]);
        $this->assertEquals('', $report[2][1]);

        $this->assertEquals('Export Project', $report[1][2]);
        $this->assertEquals('Export Project', $report[2][2]);

        $this->assertEquals('Open', $report[1][3]);
        $this->assertEquals('Open', $report[2][3]);

        $this->assertEquals('Category #1', $report[1][4]);
        $this->assertEquals('', $report[2][4]);

        $this->assertEquals('Default swimlane', $report[1][5]);
        $this->assertEquals('S1', $report[2][5]);

        $this->assertEquals('Ready', $report[1][6]);
        $this->assertEquals('Backlog', $report[2][6]);

        $this->assertEquals('Yellow', $report[1][8]);
        $this->assertEquals('Yellow', $report[2][8]);

        $this->assertEquals('', $report[1][9]);
        $this->assertEquals(date('m/d/Y').' 00:00', $report[2][9]);

        $this->assertEquals(3, $report[1][21]);
        $this->assertEquals(0, $report[2][21]);

        $this->assertEquals(2.5, $report[1][20]);
        $this->assertEquals(0, $report[2][20]);
    }
}
