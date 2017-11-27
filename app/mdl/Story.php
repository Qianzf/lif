<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Story extends ModelBase
{
    protected $table = 'story';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'role'     => 'need|string',
        'activity' => 'need|string',
        'value'    => 'need|string',
        'acceptances' => 'need|string',
        'extra'       => 'string',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function list()
    {
        return $this
        ->reset()
        ->sort([
            'create_at' => 'desc',
        ])
        ->get();
    }

    public function canBeAssignedBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
            return true;
        }

        excp('Missing user id.');
    }

    public function trendings(array $querys = [])
    {
        $relationship = [
            'model' => Trending::class,
            'lk' => 'id',
            'fk' => 'ref_id',
            'where' => [
                'ref_type' => 'story',
            ],
        ];

        if ($order = ($querys['trending'] ?? null)) {
            $relationship['sort'] = [
                'at' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

    public function addTrending(string $action)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'user'   => share('user.id'),
            'action' => $action,
            'ref_type' => 'story',
            'ref_id'   => $this->id,
        ]);
    }
}
