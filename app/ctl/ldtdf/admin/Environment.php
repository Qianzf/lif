<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Server;

class Environment extends Ctl
{
    public function list(Server $server)
    {
        view('ldtdf/env/index')->withServers($server->all());
    }

    public function edit(Server $server)
    {
        view('ldtdf/env/edit')->withServer($server);
    }

    public function create(Server $server)
    {
        if (($id = $server->create($this->request->all())) > 0) {
            share_error_i18n('CREATED_SUCCESS');
            $redirect = '/dep/admin/envs/'.$id;
        } else {
            share_error(lang('CREATE_FAILED', $id));
            $redirect = $this->route;
        }

        redirect($redirect);
    }

    public function update(Server $server)
    {
        $status = (($err = $server->save($this->request->all())) > 0)
        ? 'UPDATE_OK'
        : 'UPDATE_FAILED';

        $err = !is_integer($err) ? lang($err) : null;

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
