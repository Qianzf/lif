<?php

namespace Lif\Core\Intf;

interface SQLSchemaWorker extends SQLSchemaBuilder
{
    public function getCreator() : SQLSchemaBuilder;

    public function beforeDeath(SQLSchemaWorker $worker = null);
}
