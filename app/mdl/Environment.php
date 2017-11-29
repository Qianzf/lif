<?php

namespace Lif\Mdl;

class Environment extends Mdl
{
    protected $table = 'environment';
    protected $rules = [
        'name' => 'need|string',
        'host' => 'need|host',
        'type' => ['need|in:test,emrg,stage,prod', 'test'],
        'project' => 'need|int|min:1',
        'server'  => 'need|int|min:1',
    ];

    public function getTaskBranchHTML()
    {
        if ($task = $this->task()) {
            return "<a href='/dep/tasks/{$task->id}'>{$task->branch} / {$task->id}</a>";
        }

        return '-';
    }

    public function task()
    {
        return $this->belongsTo(
            Task::class,
            'task',
            'id'
        );
    }
}
