<?php

// -------------------------------------------
//     LiF server-side response management
// -------------------------------------------

namespace Lif\Core;

class Response
{
    protected $types = [
        'text' => 'plain/text',
        'html' => 'text/html',
        'json' => 'application/json',
        'xml'  => 'application/xml',
    ];
}
