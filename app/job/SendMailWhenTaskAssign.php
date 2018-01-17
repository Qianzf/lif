<?php

namespace Lif\Job;

class SendMailWhenTaskAssign extends \Lif\Core\Abst\Job
{
    private $task    = null;
    private $content = null;

    public function run() : bool
    {
        if (($task = $this->getTask()) && $task->alive()) {
            if (('ops' == strtolower($task->current('role')))
                && !$this->needNotifyOperator($task)
            ) {
                return true;
            }

            if (($current = $task->current())->alive()) {
                $url    = url(lrn("tasks/{$task->id}"));
                
                $title  = L('PROJECT')
                .': '
                .($task->project('name', false) ?: '-')
                .'; '
                .L($task->origin_type)
                .': '.$task->origin('title');

                $content = "<pre>{$this->content}</pre>";

                $this
                ->getSendMail()
                ->setEmails([
                    $current->email => $current->name,
                ])
                ->setTitle(L("STATUS_{$task->status}")."(T{$task->id})")
                ->setBody("<a href='{$url}'>{$title}</a>{$content}")
                ->run();
            }
        }

        return true;
    }

    public function needNotifyOperator($task)
    {
        if (in_array(strtolower($task->status), [
                'waitting_dep2test',
                'waitting_dep2stage',
                'waitting_dep2stablerc',
            ])
            && 'yes' == strtolower($task->manually)
            && ($this->content = trim($task->deploy))
        ) {
            return true;
        }

        return false;
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
