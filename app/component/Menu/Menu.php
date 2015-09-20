<?php

namespace App\Components\Menu;

use App\Components\ProjectSearchForm\ProjectSearchForm;
use Nette\Application\UI\Control;

class Menu extends Control
{
	/**
	 * Render menu
	 */
	public function render()
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

	/**
	 * Prepare project search form
	 * @return ProjectSearchForm
	 */
	public function createComponentProjectSearchForm()
	{
		return new ProjectSearchForm();
	}
}