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

    private $status = [
        'running',
        'stopped',
    ];

    public function __construct()
    {
        shares([
            'env-types'  => $this->types,
            'env-status' => $this->status,
        ]);
    }

    public function index(Env $env)
    {
        $request = $this->request->all();

        legal_or($request, [
            'page' => ['int|min:1', 1],
        ]);

        if (($type = $this->request->get('type'))
            && in_array($type, $this->types)
        ) {
            $_type = $type;
            $env = $env->whereType($type);
        } else {
            $_type = $this->types;
        }

        if (($stat = $this->request->get('status'))
            && in_array($stat, $this->status)
        ) {
            $status = $stat;
            $env = $env->whereStatus($status);
        } else {
            $status = 'all';
        }

        $keyword = $this->request->get('search') ?? null;

        if ($keyword) {
            $env = $env->whereName('like', "%{$keyword}%");
        }

        $offset  = 16;
        $start   = ($request['page'] - 1) * $offset;
        $records = $env->count();
        $envs    = $env
        ->limit($start, $offset)
        ->get();
        $pages   = ceil(($records / $offset));

        view('ldtdf/admin/env/index')
        ->withEnvsTypeStatusKeywordRecordsPagesOffset(
            $envs,
            $type,
            $status,
            $keyword,
            $records,
            $pages,
            $offset
        );
    }

    public function edit(Env $env, Server $server, Project $project)
    {
        share('hide-search-bar', true);
        
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
            share_error(L('CREATE_FAILED', L('HOST_ALREADY_EXISTS')));
        } else {
            if (($id = $env->create($this->request->all())) > 0) {
                share_error_i18n('CREATED_SUCCESS');
                $redirect = '/dep/admin/envs/'.$id;
            } else {
                share_error(L('CREATE_FAILED', $id));
            }
        }

        redirect($redirect);
    }

    public function update(Env $env)
    {
        $status = (($err = $env->save($this->request->posts())) >= 0)
        ? 'UPDATE_OK'
        : 'UPDATE_FAILED';

        $err = !is_integer($err) ? L($err) : null;

        share_error(L($status, $err));

        redirect($this->route);
    }
}
