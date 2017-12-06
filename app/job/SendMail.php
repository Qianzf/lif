<?php

namespace Lif\Job;

class SendMail extends \Lif\Core\Abst\Job
{
    private $emails  = [];
    private $title   = null;
    private $body    = null;

    public function run() : bool
    {
        email([
            'to'    => $this->emails,
            'title' => $this->title,
            'body'  => $this->body,
        ]);

        return true;
    }

    public function setEmails(array $emailsWithDispaly)
    {
        $this->emails = $emailsWithDispaly;

        return $this;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }
}
