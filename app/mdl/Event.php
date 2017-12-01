<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Event extends ModelBase
{
    protected $table = 'event';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function genDetailsOfLoginSys()
    {
    }

    public function genDetailsOfBug($id)
    {
        return [
            'route' => "/dep/bugs/{$id}",
            'title' => $this->getBugTitle($id),
        ];
    }

    public function genDetailsOfTask($id)
    {
        return [
            'route' => "/dep/tasks/{$id}",
            'title' => $this->getTaskTitle($id),
        ];
    }

    public function genDetailsOfStory($id)
    {
        return [
            'route' => "/dep/stories/{$id}",
            'title' => $this->getStoryTitle($id),
        ];
    }

    public function getBugTitle($id)
    {
        $bug = db()
        ->table('bug')
        ->select('title')
        ->whereId($id)
        ->first();

        return $bug['title'] ?? null;
    }

    public function getStoryTitle($id)
    {
        $story = db()
        ->table('story')
        ->select('title', 'id')
        ->whereId($id)
        ->first();

        return ($story['title'] ?? '')."(S{$story['id']})";
    }

    public function getTaskTitle($id)
    {
        $task = db()
        ->table('task', 't')
        ->leftJoin('story s', 't.story', 's.id')
        ->leftJoin('project p', 't.project', 'p.id')
        ->select('s.title', 'p.name', 't.id as task_id')
        ->where([
            't.id' => $id,
        ])
        ->first();

        return ($task['title'] ?? '')."(T{$task['task_id']})";
    }
}
