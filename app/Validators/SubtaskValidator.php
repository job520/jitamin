<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Subtask Validator.
 */
class SubtaskValidator extends BaseValidator
{
    /**
     * Validate creation.
     *
     * @param array $values Form values
     *
     * @return array $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = [
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('title', t('The title is required')),
        ];

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return [
            $v->execute(),
            $v->getErrors(),
        ];
    }

    /**
     * Validate modification.
     *
     * @param array $values Form values
     *
     * @return array $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = [
            new Validators\Required('id', t('The subtask id is required')),
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('title', t('The title is required')),
        ];

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return [
            $v->execute(),
            $v->getErrors(),
        ];
    }

    /**
     * Validate API modification.
     *
     * @param array $values Form values
     *
     * @return array $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateApiModification(array $values)
    {
        $rules = [
            new Validators\Required('id', t('The subtask id is required')),
            new Validators\Required('task_id', t('The task id is required')),
        ];

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return [
            $v->execute(),
            $v->getErrors(),
        ];
    }

    /**
     * Common validation rules.
     *
     * @return array
     */
    private function commonValidationRules()
    {
        return [
            new Validators\Integer('id', t('The subtask id must be an integer')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 255), 255),
            new Validators\Integer('user_id', t('The user id must be an integer')),
            new Validators\Integer('status', t('The status must be an integer')),
            new Validators\Numeric('time_estimated', t('The time must be a numeric value')),
            new Validators\Numeric('time_spent', t('The time must be a numeric value')),
        ];
    }
}
