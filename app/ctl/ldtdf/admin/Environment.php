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
        'rc',
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
        return $this->responseOnCreated(
            $env,
            lrn('/admin/envs/?'),
            function () use ($env) {
                // check if exists in code level
                if ($env->hasHostBefore($this->request->get('host'))) {
                    return 'HOST_ALREADY_EXISTS';
                }
            }
        );
    }

    public function update(Env $env)
    {
        return $this->responseOnUpdated($env);
    }
}
