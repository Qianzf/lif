<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;
use Lif\Mdl\Upload as UploadModel;

class Upload extends CtlBase
{
    private $qiniu = null;

    public function list(UploadModel $upload)
    {
        return view('ldtdf/tool/upload/index')
        ->withUploads($upload->all());
    }

    public function edit(UploadModel $upload)
    {
        $fileurl = config('qiniu.host', 'http://assets.hcmchi.com');
        $fileurl = "{$fileurl}/{$upload->filekey}";

        return view('ldtdf/tool/upload/edit')
        ->withUploadFileurl($upload, $fileurl)
        ->share('hide-search-bar', true);
    }

    public function upload()
    {
        return view('ldtdf/tool/upload/add')
        ->share('hide-search-bar', true);
    }

    public function update(UploadModel $upload)
    {
        $upload->filename  = $this->request->get('filename') ?? null;
        $upload->update_at = date('Y-m-d H:i:s');

        $msg = (($status = $upload->save()) >= 0)
        ? 'UPDATE_OK' : 'UPDATE_FAILED';

        share_error_i18n($msg);

        return redirect($this->route);
    }

    public function add(UploadModel $upload)
    {
        $status = 'new';
        $data = $this->request->posts();
        $data['user'] = share('user.id');
        $msg = (($status = $upload->create($data)) > 0)
        ? 'SAVE_OK' : 'SAVE_FAILED';

        share_error_i18n($msg);

        return redirect("/dep/tool/uploads/{$status}");
    }

    public function uptoken()
    {
        $qiniu = $this->qiniu();
        $token = $qiniu->uploadToken(
            $qiniu->bucket,
            null,
            3600
        );

        return $this->request->get('raw')
        ? json_http_response(['uptoken' => $token])
        : response(['token' => $token]);
    }

    private function qiniu()
    {
        if (! $this->qiniu) {
            $this->qiniu = $this->argvs()['Auth\Qiniu'][0] ?? null;
        }

        return $this->qiniu;
    }
}
