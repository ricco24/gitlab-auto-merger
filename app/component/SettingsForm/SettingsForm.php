<?php

namespace App\Components\SettingsForm;

use App\Model\Repository\SettingsRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PDOException;
use Tracy\Debugger;

class SettingsForm extends Control
{
	/** @var SettingsRepository */
	protected $settingsRepository;

	/** @var array */
	public $onSuccess = [];

	/**
	 * @param SettingsRepository $settingsRepository
	 */
	public function __construct(SettingsRepository $settingsRepository)
	{
		parent::__construct();
		$this->settingsRepository = $settingsRepository;
	}

	/**
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->getElementPrototype()->class('navbar-form navbar-right');
		$form->addText('gitlab_url', 'Gitlab API URL')
			->setAttribute('placeholder', 'https://domain/api/v3');
		$form->addText('gitlab_merger_url', 'GitlabMerger URL');
		$form->addText('token', 'Automerger user access token');
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] =  $this->formSuccess;

		$data = $this->settingsRepository->fetch();
		if ($data) {
			$form->setDefaults($data);
		}

		return $form;
	}

	/**
	 * Processing form
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function formSuccess($form, $values)
	{
		try {
			$data = $this->settingsRepository->fetch();
			$data ? $data->update($values) : $this->settingsRepository->insert($values);
		} catch (PDOException $e) {
			Debugger::log($e, Debugger::ERROR);
			$form->addError('Database error occurred');
			return;
		}

		$this->onSuccess();
	}

	/**
	 * Render settings form
	 */
	public function render()
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}
}