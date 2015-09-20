<?php

namespace App\Presenters;

use App\Components\SettingsForm\SettingsFormFactory;

class SettingsPresenter extends BasePresenter
{
	/** @var SettingsFormFactory @inject */
	public $settingsFormFactory;

	/**
	 * Settings form
	 * @return \App\Components\SettingsForm\SettingsForm
	 */
	public function createComponentSettingsForm()
	{
		$form = $this->settingsFormFactory->create();
		$form->onSuccess[] = function() {
			$this->flashMessage('Settings saved', 'success');
			$this->redirect('default');
		};
		return $form;
	}
}
