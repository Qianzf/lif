<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Acceptance extends ModelBase
{
    protected $table = 'acceptance';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'whose'  => 'ciin:story,bug',
        'origin' => 'int|min:1',
        'detail' => 'string',
        'status' => ['string', null],
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function updateFromOrigin(
        string $whose,
        int $origin,
        array $data = null
    )
    {
        // delete all
        $delRes = db()
        ->table($this->getTable())
        ->whereOrigin($origin)
        ->delete();

        // re-insert
        return $data
        ? $this->createFromOrigin($whose, $origin, $data)
        : $delRes;
    }

    public function createFromOrigin(
        string $whose,
        int $origin,
        array $data = null
    )
    {
        if ($data) {
            array_walk(
                $data,
                function ($item, $key) use (&$data, $whose, $origin) {
                if ($item = trim($item)) {
                    $data[$key] = [
                        'whose'  => $whose,
                        'origin' => $origin,
                        'detail' => $item,
                    ];
                } else {
                    unset($data[$key]);
                }
            });

            return ispint($this->insert($data), false);
        }
    }
}
