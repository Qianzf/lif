<?php

namespace Lif\Core\Cmd\Job;

use Lif\Core\Abst\Command;

class Add extends Command
{
    protected $intro = 'Add a job class';

    protected $option = [
        '-N'      => 'setJobName',
        '--name'  => 'setJobName',
    ];
    protected $desc = [
        'setJobName' => 'Name of job class to be created',
    ];

    private $jobName = null;

    public function fire()
    {
        if (! ($job = ucfirst($this->jobName))) {
            $this->fails('Missing job name.');
        }

        $class = underline2camelcase($job);

        if (($_class = nsOf('job', $class)) && class_exists($_class)) {
            $this->fails(
                "Job already exists: {$this->jobName}"
            );
        }

        if (!($tpl = pathOf('core', 'tpl/Job.job')) || !file_exists($tpl)) {
            excp('Framework error: template `Dit.dit` not exists.');
        }

        $_job = preg_replace_callback_array([
            '/__JOB_CLASS_NAME__/u' => function ($match) use ($class) {
                return $class;
            },
        ],
            file_get_contents($tpl)
        );

        file_put_contents(pathOf('job', "{$class}.php"), $_job)
        ? $this->success('New job: '.$_class)
        : $this->fails('Add Job failed.');
    }

    public function setJobName(string $name = null)
    {
        $this->jobName = $name;
    }
}
