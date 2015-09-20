<?php

namespace App\Helper;

class ProjectVisibility
{
	public static function process($value)
	{
		switch($value) {
			case 0:
				return '<span class="label label-danger">Private</span>';
			case 10:
				return '<span class="label label-warning">Internal</span>';
			case 20:
				return '<span class="label label-success">Public</span>';
		}
	}
}
