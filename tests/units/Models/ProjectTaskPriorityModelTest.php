<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Jitamin\Model\ProjectModel;
use Jitamin\Model\ProjectTaskPriorityModel;

require_once __DIR__.'/../Base.php';

class ProjectTaskPriorityModelTest extends Base
{
    public function testPriority()
    {
        $projectModel = new ProjectModel($this->container);
        $projectTaskPriorityModel = new ProjectTaskPriorityModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'My project 2']));
        $this->assertEquals(0, $projectTaskPriorityModel->getDefaultPriority(1));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['priority_default']);
        $this->assertEquals(0, $project['priority_start']);
        $this->assertEquals(3, $project['priority_end']);

        $this->assertEquals(
            [0 => 0, 1 => 1, 2 => 2, 3 => 3],
            $projectTaskPriorityModel->getPriorities($project)
        );

        $this->assertTrue($projectModel->update(['id' => 1, 'priority_start' => 2, 'priority_end' => 5, 'priority_default' => 4]));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(4, $project['priority_default']);
        $this->assertEquals(2, $project['priority_start']);
        $this->assertEquals(5, $project['priority_end']);

        $this->assertEquals(
            [2 => 2, 3 => 3, 4 => 4, 5 => 5],
            $projectTaskPriorityModel->getPriorities($project)
        );

        $this->assertEquals(4, $projectTaskPriorityModel->getDefaultPriority(1));
    }

    public function testGetPrioritySettings()
    {
        $projectModel = new ProjectModel($this->container);
        $projectTaskPriorityModel = new ProjectTaskPriorityModel($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'My project 2']));

        $expected = [
            'priority_default' => 0,
            'priority_start'   => 0,
            'priority_end'     => 3,
        ];

        $this->assertEquals($expected, $projectTaskPriorityModel->getPrioritySettings(1));
        $this->assertNull($projectTaskPriorityModel->getPrioritySettings(2));
    }

    public function testGetPriorityForProject()
    {
        $projectModel = new ProjectModel($this->container);
        $projectTaskPriorityModel = new ProjectTaskPriorityModel($this->container);

        $this->assertEquals(1, $projectModel->create([
            'name'             => 'My project 1',
            'priority_default' => 2,
            'priority_start'   => -2,
            'priority_end'     => 8,
        ]));

        $this->assertEquals(2, $projectTaskPriorityModel->getPriorityForProject(1, 42));
        $this->assertEquals(0, $projectTaskPriorityModel->getPriorityForProject(1, 0));
        $this->assertEquals(1, $projectTaskPriorityModel->getPriorityForProject(1, 1));
        $this->assertEquals(-2, $projectTaskPriorityModel->getPriorityForProject(1, -2));
        $this->assertEquals(-1, $projectTaskPriorityModel->getPriorityForProject(1, -1));
        $this->assertEquals(8, $projectTaskPriorityModel->getPriorityForProject(1, 8));
        $this->assertEquals(5, $projectTaskPriorityModel->getPriorityForProject(1, 5));
        $this->assertEquals(2, $projectTaskPriorityModel->getPriorityForProject(1, 9));
        $this->assertEquals(2, $projectTaskPriorityModel->getPriorityForProject(1, -3));
    }
}
