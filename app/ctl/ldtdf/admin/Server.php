<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Server as ServerModel;

class Server extends Ctl
{
    public function __construct()
    {
        share('hide-search-bar', true);
    }

    public function index(ServerModel $server)
    {
        view('ldtdf/admin/server/index')
        ->withServers($server->all())
        ->share('hide-search-bar', false);
    }

    public function edit(ServerModel $server)
    {      
        view('ldtdf/admin/server/edit')
        ->withServer($server);
    }

    public function create(ServerModel $server)
    {
        return $this->responseOnCreated($server, '/dep/admin/servers/?');
    }

    public function update(ServerModel $server)
    {
        return $this->responseOnUpdated($server);
    }
}
