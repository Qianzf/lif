<?php

namespace Lif\Mdl;

class Environment extends Mdl
{
    protected $table = 'environment';
    protected $rules = [
        'host' => 'need|host',
        'type' => ['need|ciin:test,emrg,stage,rc,prod', 'test'],
        'project' => 'need|int|min:1',
        'server'  => 'need|int|min:1',
        'desc' => 'string',
    ];

    public function getTaskBranchHTML()
    {
        if ($tasks = $this->tasks()) {
            $html = '';
            foreach ($tasks as $task) {
               $html .= "<a href='/dep/tasks/{$task->id}'>{$task->branch} / {$task->id}</a>";

               if (false !== next($tasks)) {
                    $html .= '; ';
               }
            }

            return $html;
        }

        return '-';
    }

    public function tasks()
    {
        return $this->hasMany(
            Task::class,
            'id',
            'env'
        );
    }

    public function projects()
    {
        return $this->hasMany(
            Project::class,
            'project',
            'id'
        );   
    }

    public function server()
    {
        return $this->belongsTo(
            Server::class,
            'server',
            'id'
        );
    }
}
