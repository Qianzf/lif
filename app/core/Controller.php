<?php

namespace Lif\Core;

class Controller
{
	use \Lif\Core\Traits\Tol;

	protected $app = null;

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function __get($name)
	{
		return $this->app->$name;
	}
}
