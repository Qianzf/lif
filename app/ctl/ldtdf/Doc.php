<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{Doc as DocModel, DocFolder, User};

class Doc extends Ctl
{
    public function __construct()
    {
        share('hide-search-bar', true);
    }

    public function getChildren(DocFolder $folder)
    {
        $children = $folder->getTreeSelectFormattedList(
            $this->request->get('id')
        );

        return $this->request->has('dat-only')
        ? json_http_response($children)
        : response($children);
    }

    public function queryFolderChildren(DocFolder $folder)
    {
        if (! $folder->alive()) {
            return client_error('DOC_FOLDER_NOT_FOUND', 404);
        }

        $data['children'] = $folder->children(['id', 'title'], false);
        if (! $this->request->has('folder-only')) {
            $data['docs'] = $folder->docs(false);
        }

        return response($data);
    }

    public function my(DocModel $doc)
    {
        dd($doc->ofUser(share('user.id')));
    }

    public function index(DocFolder $folder, DocModel $doc)
    {
        $querys = $this->request->gets();

        legal_or($querys, [
            'search' => ['string', null],
        ]);

        view('ldtdf/docs/index')
        ->withFoldersDocs(
            $folder->list([
                'parent' => 0,
            ], $querys),

            $doc->list([
                'folder' => 0,
            ], $querys)
        )
        ->share('hide-search-bar', false);
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
            $folder->make($this->request->get('folder')),
            $folder->getTreeSelectFormattedList()
        )
        ->share('hide-search-bar', true);
    }

    public function create(DocModel $doc)
    {
        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated(
            $doc,
            '/dep/docs/?',
            null,
            function () use ($doc) {
            $doc->addTrending('create', share('user.id'));
        });
    }

    public function update(DocModel $doc)
    {
        return $this->responseOnUpdated(
            $doc,
            null,
            function () use ($doc) {
                $this->request->setPost('update_at', fndate());

                if ($doc->creator('id') != share('user.id')) {
                    return 'UPDATE_PERMISSION_DENIED';
                }
            },
            function () use ($doc) {
                $doc->addTrending('update', share('user.id'));
            }
        );
    }

    public function viewFolder(DocFolder $folder)
    {
        if (ispint($doc = $this->request->get('doc'))) {
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
            $folder->make($this->request->get('parent')),
            $folder->getTreeSelectFormattedList()
        );
    }

    public function updateFolder(DocFolder $folder)
    {
        return $this->responseOnUpdated(
            $folder,
            null,
            function () use ($folder) {
                if ($folder->creator('id') != share('user.id')) {
                    return 'UPDATE_PERMISSION_DENIED';
                }

                if ($folder->id == $this->request->get('parent')) {
                    $this->request->setPost('parent', $folder->parent);
                }
            },
            function () use ($folder) {
                $folder->addTrending('update', share('user.id'));
            }
        );
    }

    public function createFolder(DocFolder $folder)
    {
        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated(
            $folder,
            '/dep/docs/folders/?/edit',
            null,
            function () use ($folder) {
                $folder->addTrending('create', share('user.id'));
            }
        );
    }
}
