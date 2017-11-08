<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Server as ServerModel;

class Server extends Ctl
{
    public function list(ServerModel $server)
    {
        view('ldtdf/server/index')->withServers($server->all());
    }

    public function edit(ServerModel $server)
    {
        view('ldtdf/server/edit')->withServer($server);
    }

    public function create(ServerModel $server)
    {
        if (($id = $server->create($this->request->all())) > 0) {
            share_error_i18n('CREATED_SUCCESS');
            $redirect = '/dep/admin/servers/'.$id;
        } else {
            share_error(lang('CREATE_FAILED', $id));
            $redirect = $this->route;
        }

        redirect($redirect);
    }

    public function update(ServerModel $server)
    {
        $status = (($err = $server->save($this->request->all())) > 0)
        ? 'UPDATE_OK'
        : 'UPDATE_FAILED';

        $err = !is_integer($err) ? lang($err) : null;

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
