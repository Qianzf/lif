<?php

namespace Lif\Core\Intf;

interface SQLSchemaWorker extends SQLSchemaBuilder
{
    public function ofCreator(SQLSchemaBuilder $creator) : SQLSchemaBuilder;

    public function getCreator() : SQLSchemaBuilder;

    public function beforeDeath(SQLSchemaWorker $worker = null);
    
    public function fulfillWishFor(SQLSchemaWorker $worker = null);
}
