<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function list(array $params)
    {
        $role = (share('user.role') == 'admin')
        ? -1 : 'admin';

        legal_or($params, [
            'uid'  => ['int|min:1', null],
            'from' => ['int|min:0', 0],
            'take' => ['int|min:0', 20],
        ]);

        if (is_null($params['uid'])) {
            return $this
            ->leftJoin('user', 'user.id', 'trending.uid')
            ->sort([
                'trending.at' => 'desc',
            ])
            ->where('user.role', '!=', $role)
            ->limit(
                $params['from'],
                $params['take']
            )
            ->get();
        }

        $user = model(User::class, $params['uid']);

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
        $event   = $this->makeEvent();
        $key     = underline2camelcase($this->event);
        $handler = "genHTMLStringOf{$key}";

        if (! method_exists($event, $handler)) {
            excp("Event string generator not found: {$handler}()");
        }
        
        $data = call_user_func([$event, $handler], $this->ref_id);

        $route = $data['route'] ?? null;
        $title = $data['title'] ?? null;

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
            'uid',
            'id'
        );
    }
}
