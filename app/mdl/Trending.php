<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function add(
        string $aciton,
        int $user,
        string $refType = null,
        string $refID = null
    ) {
        return $this->insert([
            'at'       => date('Y-m-d H:i:s'),
            'user'     => $user,
            'action'   => $aciton,
            'ref_type' => $refType,
            'ref_id'   => $refID,
        ]);
    }

    public function list(array $params)
    {
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

        return $user->trendings($params['from'], $params['take']);
    }

    public function genHTMLStringOfEvent(
        bool $displayRefType = null,
        bool $displayRefState = null
    ) : string
    {
        $html = L($this->action);

        if ($displayRefType) {
            $html .= L($this->ref_type);
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

            $html .= $data ? ": <i><a href='{$route}'>{$title}</a></i> " : '';
        }

        if ($this->target && ($target = model(User::class, $this->target))) {
            $html .= L('TO').L("ROLE_{$target->role}");
            $html .= " <i><a href='/dep/users/{$target->id}'>{$target->name}</a></i>";
        }

        if ($displayRefState && ($status = trim($this->ref_state))) {
            $html .= ' <sup><i class="text-status">( '
            .L("STATUS_{$status}")
            .' )</sup></i>';
        }

        return $html;
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
