<?php

namespace App\Components\ProjectForm;

use App\Model\Repository\ProjectRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\IRow;
use Nette\Utils\ArrayHash;
use PDOException;
use Tracy\Debugger;

class ProjectForm extends Control
{
	/** @var ProjectRepository */
	protected $projectRepository;

	/** @var IRow */
	protected $project;

	/** @var int */
	protected $gitlabId;

	/** @var string */
	protected $gitlabName;

	/** @var string */
	protected $gitlabUrl;

	/** @var array */
	public $onSuccess = [];

	/**
	 * @param ProjectRepository $projectRepository
	 */
	public function __construct(ProjectRepository $projectRepository)
	{
		parent::__construct();
		$this->projectRepository = $projectRepository;
	}

	/**
	 * @param IRow $project
	 */
	public function setProject(IRow $project)
	{
		$this->project = $project;
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param string $url
	 */
	public function setGitlabData($id, $name, $url)
	{
		$this->gitlabId = $id;
		$this->gitlabName = $name;
		$this->gitlabUrl = $url;
	}

	/**
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->getElementPrototype()->class('navbar-form navbar-right');
		$form->addText('positive_votes', 'Positive votes to merge')
			->setRequired('%label is require')
			->addRule(Form::INTEGER, '%label has to be an integer');
		$form->addCheckbox('delete_source_branch', 'Remove source branch when merge request is accepted');
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] =  $this->formSuccess;

		if ($this->project) {
			$form->setDefaults($this->project);
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
			if ($this->project) {
				$this->project->update($values);
			} elseif ($this->gitlabId) {
				$this->projectRepository->insert([
					'gitlab_id' => $this->gitlabId,
					'url' => $this->gitlabUrl,
					'name' => $this->gitlabName,
					'positive_votes' => $values['positive_votes'],
					'delete_source_branch' => $values['delete_source_branch']
				]);
			}
		} catch (PDOException $e) {
			Debugger::log($e, Debugger::ERROR);
			$form->addError('Database error occurred');
			return;
		}

		$this->onSuccess();
	}

	/**
	 * Render project form
	 */
	public function render()
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}
}