<?php

namespace Lif\Job;

class SendMailWhenTaskAssign extends \Lif\Core\Abst\Job
{
    private $task = null;

    public function run() : bool
    {
        if (($task = $this->getTask())->alive()) {
            if (($current = $task->current())->alive()) {
                $url    = url("dep/tasks/{$task->id}");
                $title  = L('PROJECT')
                .': '
                .$task->project()->name
                .'; '
                .L($task->origin_type)
                .': '.$task->origin()->title;

                $this
                ->getSendMail()
                ->setEmails([
                    // $current->email => $current->name,
                    'lcj@hcmchi.cn' => 'LCJ',
                ])
                ->setTitle("(T{$task->id})".L("STATUS_{$task->status}"))
                ->setBody("<a href='{$url}'>{$title}</a>")
                ->run();
            }
        }

        return true;
    }

    public function getTask()
    {
        return new \Lif\Mdl\Task($this->task);
    }

    public function getSendMail()
    {
        return new SendMail;
    }

    public function setTask(int $task)
    {
        $this->task = $task;

        return $this;
    }
}
