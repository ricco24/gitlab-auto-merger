<?php

namespace App\Helper;

class EnabledStatus
{
	public static function process($value)
	{
		return $value
			? '<span class="label label-success">Enabled</span>'
			: '<span class="label label-danger">Disabled</span>';
	}
}
