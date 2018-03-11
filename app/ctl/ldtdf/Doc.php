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

    public function index(DocFolder $folder, DocModel $doc, User $user)
    {
        $querys = $this->request->gets();

        legal_or($querys, [
            'search'  => ['string', null],
            'creator' => ['int|min:1', null],
            'sort'    => ['ciin:desc,asc', 'desc'],
        ]);

        $users = $user->list(['id', 'name'], null, false);

        view('ldtdf/docs/index')
        ->withFoldersDocsUsers(
            $folder->list([
                'parent' => 0,
            ], $querys),

            $doc->list([
                'folder' => 0,
            ], $querys),

            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            )
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
        return view('ldtdf/docs/edit')
        ->withDocFolderFoldersParent(
            $doc,
            $folder->make($this->request->get('folder')),
            $folder->getTreeSelectFormattedList(),
            $this->request->get('parent')
        )
        ->share('hide-search-bar', true);
    }

    public function create(DocModel $doc)
    {
        $this->request->setPost('creator', share('user.id'));

        $docShow = (true
            && ($folder = $this->request->get('parent'))
            && ispint($folder, true)
        ) ? "docs/folders/{$folder}%3Fdoc=?" : "docs/?";

        return $this->responseOnCreated(
            $doc,
            lrn($docShow),
            null,
            function () use ($doc) {
                $doc->addTrending('create', share('user.id'));
            }
        );
    }

    public function update(DocModel $doc)
    {
        $docShow = (true
            && ($folder = $this->request->get('parent'))
            && ispint($folder, true)
        ) ? "docs/folders/{$folder}?doc={$doc->id}" : "docs/{$doc->id}";

        return $this->responseOnUpdated(
            $doc,
            lrn($docShow),
            function () use ($doc) {
                if ($doc->alive()) {
                    if ($doc->creator('id') != share('user.id')) {
                        return 'UPDATE_PERMISSION_DENIED';
                    }

                    if (($doc->title != $this->request->posts('title'))
                        || ($doc->content != $this->request->posts('content'))
                        || ($doc->folder != $this->request->posts('folder'))
                        || ($doc->order != $this->request->posts('order'))
                    ) {
                        $this->request->setPost('update_at', fndate());
                    }
                }
            },
            function ($status) use ($doc) {
                if (ispint($status, false)) {
                    $doc->addTrending('update', share('user.id'));
                }
            }
        );
    }

    public function viewFolder(DocFolder $folder)
    {
        $unfoldables = [];

        if (ispint($doc = $this->request->get('doc'), true)) {
            $doc = model(DocModel::class, $doc);
            $unfoldables = $this->getUnfoldableDocs($folder, $doc);
        } else {
            if (ispint($_folder = $this->request->get('folder'), true)) {
                $unfoldables = $this->getUnfoldableFolders($folder, $_folder);
            }

            $doc = $folder->firstDoc() ?? model(DocModel::class);
        }

        return view('ldtdf/docs/folder/view')
        ->withFolderDocsChildrenDocUnfoldables(
            $folder,
            $folder->docs(),
            $folder->children(),
            $doc,
            $unfoldables
        );
    }

    protected function getUnfoldableFolders($folder, $_folder)
    {
        $arr = [];

        if (true
            && $folder->id
            && $_folder
            && ($__folder = $folder->make($_folder))
            && ($parent = $__folder->parent())
        ) {
            $arr[] = $__folder->id;
            $arr[] = $parent->id;

            while (true
                && ($parent = $parent->parent())
                && ($parent->id != $folder->id)
            ) {
                $arr[] = $parent->id;
            }
        }

        return $arr;
    }

    protected function getUnfoldableDocs($folder, $doc)
    {
        $arr = [];

        if ($folder->id && $doc->id && ($parent = $doc->folder())) {
            $arr[] = $parent->id;

            while (true
                && ($parent = $parent->parent())
                && ($parent->id != $folder->id)
            ) {
                $arr[] = $parent->id;
            }
        }

        return $arr;
    }

    public function editFolder(DocFolder $folder)
    {
        return view('ldtdf/docs/folder/edit')
        ->withFolderParentFolders(
            $folder,
            $folder->make($this->request->get('parent')),
            $folder->getTreeSelectFormattedList()
        );
    }

    public function updateFolder(DocFolder $folder)
    {
        $folderId = (true
            && ($parent = $this->request->get('parent'))
            && ispint($parent, true)
        ) ? $parent : $folder->id;

        return $this->responseOnUpdated(
            $folder,
            lrn("docs/folders/{$folderId}"),
            function () use ($folder) {
                if ($folder->creator('id') != share('user.id')) {
                    return 'UPDATE_PERMISSION_DENIED';
                }

                if (($folder->title != $this->request->posts('title'))
                    || ($folder->desc != $this->request->posts('desc'))
                    || ($folder->parent != $this->request->posts('parent'))
                    || ($folder->order != $this->request->posts('order'))
                ) {
                    $this->request->setPost('update_at', fndate());
                }

                if ($folder->id == $this->request->get('parent')) {
                    $this->request->setPost('parent', $folder->parent);
                }
            },
            function ($status) use ($folder) {
                if (ispint($status, false)) {
                    $folder->addTrending('update', share('user.id'));
                }
            }
        );
    }

    public function createFolder(DocFolder $folder)
    {
        $showFolder = (true
            && ($parent = $this->request->get('folder'))
            && ispint($parent, true)
        ) ? "docs/folders/{$parent}%3Ffolder=?" : 'docs/folders/?';

        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated(
            $folder,
            lrn($showFolder),
            null,
            function () use ($folder) {
                $folder->addTrending('create', share('user.id'));
            }
        );
    }
}
