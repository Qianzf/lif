<?php

namespace Lif\Traits;

trait TasksOriginable
{
    public function product(string $key = null, bool $excp = false)
    {
        $product = $this->belongsTo(
            \Lif\Mdl\Product::class,
            'product',
            'id'
        );

        if ($excp && !$product) {
            excp(L('MISSING_PRODUCT'));
        }

        return $key ? ($product ? $product->$key : null) : $product;
    }
    
    public function cancelRelateTasks(array $toCancel)
    {
        if ($this->alive() && $toCancel) {
            $task = db()->table('task');
            $task->where([
                'origin_type' => $this->getTaskOriginName(),
                'origin_id'   => $this->id,
                'first_dev'   => $toCancel,
                [db()->native('LOWER(`status`)'), 'not in', [
                    'canceled',
                    'online',
                    'finished',
                ]],
            ])
            // ->setIfExecuteStatement(false)
            // ->setIfOutputStatement(2)
            ->update([
                'status'  => 'canceled',
                'branch'  => null,
                'current' => null,
            ]);

            $this->addTrendingsForOperatingTasks(
                $task,
                'cancel',
                'canceled',
                L('STORY_CREATOR_CANCEL_WITHOUT_REASON'),
                ['first_dev' => $toCancel]
            );

            return true;
        }   
    }

    private function getOperatingTaskIds($task, array $where = [])
    {
        $query = $task
        ->reset()
        ->select('id')
        ->where([
            'origin_type' => $this->getTaskOriginName(),
            'origin_id'   => $this->id,
            'creator'     => $this->creator,
        ]);

        if ($where) {
            $query = $query->where($where);
        }

        return $query->get();
    }

    private function addTrendingsForOperatingTasks(
        $task,
        string $action,
        string $status,
        string $notes = null,
        array $where = []
    )
    {
        $tids = $this->getOperatingTaskIds($task, $where);

        $trendings = [];
        foreach ($tids as $tid) {
            $trendings[] = [
                'at'        => fndate(),
                'user'      => $this->creator,
                'action'    => $action,
                'ref_type'  => 'task',
                'ref_id'    => ($tid['id'] ?? null),
                'ref_state' => $status,
                'notes'     => $notes,
            ];
        }

        if ($trendings) {
            db()->table('trending')->insert($trendings);
        }
    }

    public function reActivateRelatedCanceledTasks(
        string $status,
        array $toActivate = null,
        array $where = []
    )
    {
        if ($toActivate) {
            $query = db()
            ->table('task')
            ->reset()
            ->where([
                [db()->native('LOWER(`status`)'), 'canceled'],
                'origin_type' => $this->getTaskOriginName(),
                'origin_id'   => $this->id,
                'first_dev'   => $toActivate,
            ]);

            if ($where) {
                $query = $query->where($where);
            }

            $updated = $query->update([
                'status'  => strtolower($status),
                'current' => db()->native('`first_dev`'),
            ]);

            if (ispint($updated, false)) {
                $this->addTrendingsForOperatingTasks(
                    $query,
                    'activate',
                    $status,
                    null,
                    $where
                );
            }
        }

        return $this;
    }

    public function tryActivateRelatedTasks(array $toActivate = null)
    {
        if ($toActivate) {
            $this
            ->reActivateRelatedCanceledTasks(
                'activated', $toActivate, [
                ['project', '>', '0'],
            ])
            ->reActivateRelatedCanceledTasks(
                'waiting_edit', $toActivate, [
                ['project', '<', '1'],
            ]);
        }

        return true;
    }

    public function createTasks(array $tasks)
    {
        if ($this->alive()) {
            $task = db()->table('task');
            $task->insert($tasks);

            $this->addTrendingsForOperatingTasks(
                $task,
                'create',
                'waiting_edit'
            );

            return true;
        }
    }

    public function getPrincipals(array $where = [])
    {
        if ($this->alive()) {
            $query = db()
            ->table('user')
            ->select('user.id')
            ->leftJoin('task', 'user.id', 'task.first_dev')
            ->where([
                'task.origin_type' => $this->getTaskOriginName(),
                'task.origin_id'   => $this->id,
            ]);

            if ($where) {
               $query = $query->where($where);
            }

            return $query->get();
        }
    }
}