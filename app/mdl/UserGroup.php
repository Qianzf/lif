<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class UserGroup extends ModelBase
{
    protected $table = 'user_group';
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

    public function hasSameGroup(string $name) : bool
    {
        if ($name = trim($name)) {
            return db()
            ->table($this->table)
            ->whereName($name)
            ->count() > 0;
        }

        return false;
    }

    public function writeWithUsers(array $data, int $group = null) : bool
    {
        $create = is_null($group);
        $users = $data['users'] ?? [];
        unset($data['users']);

        db()->start();
        $update = $this->save($data);
        if ($create) {
            $group = $update;
        }
        $delete = db()
        ->table('user_group_map')
        ->whereGroup($group)
        ->delete();

        array_walk($users, function (&$item) use ($group) {
            $item = [
                'user'  => $item,
                'group' => $group,
            ];
        });

        $insert = db()->table('user_group_map')->insert($users);

        if (($update >= 0) && ($delete >= 0)  && ($insert > 0)) {
            db()->commit();
            return $create ? $update : true;
        }

        db()->rollback();
        return false;
    }

    public function createWithUsers(array $data) : bool
    {
        $this->reset();

        return $this->writeWithUsers($data);
    }

    public function updateWithUsers(array $data) : bool
    {
        if (! $this->alive() || !($group = $this->getPK())) {
            excp('Can not update user group when group is not alive.');
        }

        return $this->writeWithUsers($data, $group);
    }
}
