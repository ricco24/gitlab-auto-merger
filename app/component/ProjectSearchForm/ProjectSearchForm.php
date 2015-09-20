<?php

namespace App\Components\ProjectSearchForm;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class ProjectSearchForm extends Control
{
	/**
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->getElementPrototype()->class('navbar-form navbar-right');
		$form->addText('text')
			->setAttribute('placeholder', 'Search project')
			->getControlPrototype()
				->class('form-control');
		$form->addSubmit('search', 'Search')
			->getControlPrototype()
				->class('btn btn-success');
		$form->onSuccess[] =  $this->formSuccess;

		return $form;
	}

	/**
	 * Processing form
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function formSuccess($form, $values)
	{
		$this->presenter->redirect('Project:search', [
			'text' => $values['text']
		]);
	}

	/**
	 * Render search form
	 */
	public function render()
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}
}