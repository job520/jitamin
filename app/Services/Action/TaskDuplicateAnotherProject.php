<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Action;

use Jitamin\Model\TaskModel;

/**
 * Duplicate a task to another project.
 */
class TaskDuplicateAnotherProject extends Base
{
    /**
     * Get automatic action description.
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Duplicate the task to another project');
    }

    /**
     * Get the list of compatible events.
     *
     * @return array
     */
    public function getCompatibleEvents()
    {
        return [
            TaskModel::EVENT_MOVE_COLUMN,
            TaskModel::EVENT_CLOSE,
            TaskModel::EVENT_CREATE,
        ];
    }

    /**
     * Get the required parameter for the action (defined by the user).
     *
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return [
            'column_id'  => t('Column'),
            'project_id' => t('Project'),
        ];
    }

    /**
     * Get the required parameter for the event.
     *
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return [
            'task_id',
            'task' => [
                'project_id',
                'column_id',
            ],
        ];
    }

    /**
     * Execute the action (duplicate the task to another project).
     *
     * @param array $data Event data dictionary
     *
     * @return bool True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $destination_column_id = $this->columnModel->getFirstColumnId($this->getParam('project_id'));

        return (bool) $this->taskProjectDuplicationModel->duplicateToProject(
            $data['task_id'],
            $this->getParam('project_id'),
            null,
            $destination_column_id
        );
    }

    /**
     * Check if the event data meet the action condition.
     *
     * @param array $data Event data dictionary
     *
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        return $data['task']['column_id'] == $this->getParam('column_id') && $data['task']['project_id'] != $this->getParam('project_id');
    }
}
