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

    public function genDetailsOfDoc($id)
    {
        return [
            'route' => lrn("docs/{$id}"),
            'title' => $this->getDocTitle($id),
        ];
    }

    public function genDetailsOfDocFolder($id)
    {
        return [
            'route' => lrn("docs/folders/{$id}"),
            'title' => $this->getDocFolderTitle($id),
        ];
    }

    public function getDocTitle($id)
    {
        $doc = db()
        ->table('doc')
        ->select('title', 'id')
        ->whereId($id)
        ->first();

        return ($doc['title'] ?? '')."(D{$doc['id']})";
    }

    public function getDocFolderTitle($id)
    {
        $folder = db()
        ->table('doc_folder')
        ->select('title', 'id')
        ->whereId($id)
        ->first();

        return ($folder['title'] ?? '')."(F{$folder['id']})";
    }

    public function genDetailsOfLoginSys()
    {
    }

    public function genDetailsOfBug($id)
    {
        return [
            'route' => lrn("bugs/{$id}"),
            'title' => $this->getBugTitle($id),
        ];
    }

    public function genDetailsOfTask($id)
    {
        return [
            'route' => lrn("tasks/{$id}"),
            'title' => $this->getTaskTitle($id),
        ];
    }

    public function genDetailsOfStory($id)
    {
        return [
            'route' => lrn("stories/{$id}"),
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
        $task  = model(Task::class, $id);
        $title = $task->origin('title', $task->origin_type);
        
        return ($title ?? '')."(T{$id})";
    }
}
