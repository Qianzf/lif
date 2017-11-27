<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function add(
        string $aciton,
        string $refType = null,
        string $refID = null
    ) {
        return $this->insert([
            'at'       => date('Y-m-d H:i:s'),
            'user'     => share('user.id'),
            'action'   => $aciton,
            'ref_type' => $refType,
            'ref_id'   => $refID,
        ]);
    }

    public function list(array $params)
    {
        // $role = (share('user.role') == 'admin') ? -1 : 'admin';

        legal_or($params, [
            'user' => ['int|min:1', null],
            'from' => ['int|min:0', 0],
            'take' => ['int|min:0', 20],
        ]);

        if (is_null($params['user'])) {
            return $this
            ->leftJoin('user', 'user.id', 'trending.user')
            ->sort([
                'trending.at' => 'desc',
            ])
            // ->where('user.role', '!=', $role)
            ->limit(
                $params['from'],
                $params['take']
            )
            ->get();
        }

        $user = model(User::class, $params['user']);

        if (! $user->items()) {
            client_error('USER_NOT_FOUND', 404);
        }
        if (($user->role == 'admin') && (share('user.role') != 'admin')) {
            return [];
        }

        return $user->trendings($params['from'], $params['take']);
    }

    public function genHTMLStringOfEvent() : string
    {
        $event = $this->makeEvent();
        $data  = false;
        
        if ($key = ucfirst($this->ref_type)) {
            $handler = "genDetailsOf{$key}";
            if (! method_exists($event, $handler)) {
                excp("Event string generator not found: {$handler}()");
            }
            
            $data = call_user_func([$event, $handler], $this->ref_id);

            $route = $data['route'] ?? null;
            $title = $data['title'] ?? null;
        }

        return $data ? ": <a href='{$route}'>{$title}</a>" : '';
    }

    public function makeEvent()
    {
        return new Event;
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user',
            'id'
        );
    }
}
