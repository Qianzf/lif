<?php

namespace Lif\Core\Excp;

class NonExistsRelationship extends \Exception
{
    public function __construct()
    {
        $this->message = 'Non-exists model can not has any relationship.';
    }
}
