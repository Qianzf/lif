<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Environment as Env;
use Lif\Mdl\Server;

class Environment extends Ctl
{
    private $types = [
        'test',
        'stage',
        'prod',
    ];

    public function __construct()
    {
        share('env-types', $this->types);
    }

    public function list(Env $env)
    {
        if (($type = $this->request->get('type'))
            && in_array($type, $this->types)
        ) {
            $_type = $type;
        } else {
            $_type = [
                'test',
                'stage',
                'prod',
            ];
        }

        $env     = $env->whereType($_type);
        $keyword = $this->request->get('search') ?? null;

        if ($keyword) {
            $env = $env->whereName('like', "%{$keyword}%");
        }

        $envs = $env->get();

        view('ldtdf/env/index')->withEnvsTypeKeyword($envs, $type, $keyword);
    }

    public function edit(Env $env, Server $server)
    {
        $servers = $server->all();

        view('ldtdf/env/edit')->withEnvServers($env, $servers);
    }

    public function create(Env $env)
    {
        $redirect = $this->route;

        // check if exists in code level
        if ($env->whereHost($this->request->get('host'))->count()) {
            share_error(lang('CREATE_FAILED', lang('HOST_ALREADY_EXISTS')));
        } else {
            if (($id = $env->create($this->request->all())) > 0) {
                share_error_i18n('CREATED_SUCCESS');
                $redirect = '/dep/admin/envs/'.$id;
            } else {
                share_error(lang('CREATE_FAILED', $id));
            }
        }

        redirect($redirect);
    }

    public function update(Env $env)
    {
        $status = (($err = $env->save($this->request->all())) > 0)
        ? 'UPDATE_OK'
        : 'UPDATE_FAILED';

        $err = !is_integer($err) ? lang($err) : null;

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
