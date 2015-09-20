<?php

namespace App\Helper;

use Nette\Object;
use Nette\Utils\Strings;

class FilterLoader extends Object
{
	/** @var array			All registered filters */
	private $filters = array();

	/**
	 * Check if filter is registered, call filter if is registered
	 * @param string $helper
	 * @return mixed
	 */
	public function load($helper)
	{
		if (isset($this->filters[$helper])) {
			return call_user_func_array($this->filters[$helper], array_slice(func_get_args(), 1));
		}
	}

	/**
	 * Registers new filter
	 * @param string $name
	 * @param callback $callback
	 */
	public function register($name, $callback)
	{
		$this->filters[Strings::lower($name)] = $callback;
	}
}
