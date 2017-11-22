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

    public function genDetailsforBug($id)
    {
        return [
            'route' => "/dep/bugs/{$id}",
            'title' => $this->getBugTitle($id),
        ];
    }

    public function genDetailsforTask($id)
    {
        return [
            'route' => "/dep/tasks/{$id}",
            'title' => $this->getTaskTitle($id),
        ];
    }

    public function genDetailsOfUpdateBugComment($id)
    {
        return $this->genDetailsforBug($id);
    }

    public function genDetailsOfUpdateTaskComment($id)
    {
        return $this->genDetailsforTask($id);
    }

    public function genDetailsOfCreateTask($id)
    {
        return $this->genDetailsforTask($id);
    }

    public function genDetailsOfAssignTask($id)
    {
        return $this->genDetailsforTask($id);
    }

    public function genDetailsOfUpdateTask($id)
    {
        return $this->genDetailsforTask($id);
    }

    public function getBugTitle($id)
    {
        $bug = db()->table('bug')->select('title')->whereId($id)->first();

        return $bug['title'] ?? null;
    }

    public function getTaskTitle($id)
    {
        $task = db()->table('task')->select('title')->whereId($id)->first();

        return $task['title'] ?? null;
    }
}
