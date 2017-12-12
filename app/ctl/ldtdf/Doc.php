<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{Doc as DocModel, DocFolder, User};

class Doc extends Ctl
{
    public function queryFolderChildren(DocFolder $folder)
    {
        if (! $folder->isAlive()) {
            return client_error('DOC_FOLDER_NOT_FOUND', 404);
        }

        return response([
            'docs'     => $folder->docs(false),
            'children' => $folder->children(['id', 'title'], false),
        ]);
    }

    public function my(DocModel $doc)
    {
        dd($doc->ofUser(share('user.id')));
    }

    public function index(DocFolder $folder, DocModel $doc)
    {
        view('ldtdf/docs/index')
        ->withFoldersDocs(
            $folder->whereParent(0)->get(),
            $doc->whereFolder(0)->get()
        );
    }

    public function viewDoc(DocModel $doc)
    {
        view('ldtdf/docs/view')
        ->withDoc($doc)
        ->share('hide-search-bar', true);
    }

    public function edit(DocModel $doc, DocFolder $folder)
    {
        view('ldtdf/docs/edit')
        ->withDocFolderFolders(
            $doc,
            $this->request->get('folder'),
            $folder->all()
        );
    }

    public function create(DocModel $doc)
    {
        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated($doc, '/dep/docs/?');
    }

    public function update(DocModel $doc)
    {
        return $this->responseOnUpdated($doc);
    }

    public function viewFolder(DocFolder $folder)
    {
        if ($doc = ispint($this->request->get('doc'))) {
            $doc = model(DocModel::class, $doc);
        } else {
            $doc = $folder->firstDoc() ?? model(DocModel::class);
        }

        view('ldtdf/docs/folder/view')
        ->withFolderDocsChildrenDoc(
            $folder,
            $folder->docs(),
            $folder->children(),
            $doc
        );
    }

    public function editFolder(DocFolder $folder)
    {
        view('ldtdf/docs/folder/edit')
        ->withFolderParentFolders(
            $folder,
            $this->request->get('parent'),
            $folder->listOthers()
        );
    }

    public function updateFolder(DocFolder $folder)
    {
        return $this->responseOnUpdated($folder);
    }

    public function createFolder(DocFolder $folder)
    {
        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated($folder, '/dep/docs/folders/?/edit');
    }
}
