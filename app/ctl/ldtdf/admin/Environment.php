<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Environment as Env;
use Lif\Mdl\{Server, Project};

class Environment extends Ctl
{
    private $types = [
        'test',
        'emrg',
        'stage',
        'prod',
    ];

    public function __construct()
    {
        share('env-types', $this->types);
    }

    public function index(Env $env)
    {
        if (($type = $this->request->get('type'))
            && in_array($type, $this->types)
        ) {
            $_type = $type;
        } else {
            $_type = $this->types;
        }

        $env     = $env->whereType($_type);
        $keyword = $this->request->get('search') ?? null;

        if ($keyword) {
            $env = $env->whereName('like', "%{$keyword}%");
        }

        $envs = $env->get();

        view('ldtdf/admin/env/index')
        ->withEnvsTypeKeyword($envs, $type, $keyword);
    }

    public function edit(Env $env, Server $server, Project $project)
    {
        $servers  = $server->all();
        $projects = $project->all();

        view('ldtdf/admin/env/edit')
        ->withEnvServersProjects($env, $servers, $projects);
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
