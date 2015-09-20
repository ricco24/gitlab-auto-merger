<?php

namespace App\Presenters;

use App\Components\ProjectForm\ProjectFormFactory;
use App\Model\GitLab\MergeRequestBuilder;
use App\Model\Repository\ProjectRepository;
use Gitlab\Client;
use Nette\Application\BadRequestException;
use Nette\Http\Request;
use App\Components\ProjectForm\ProjectForm;

class ProjectPresenter extends BasePresenter
{
	/** @var Client @inject */
	public $gitlabClient;

	/** @var Request @inject */
	public $httpRequest;

	/** @var MergeRequestBuilder @inject */
	public $mergeRequestBuilder;

	/** @var ProjectFormFactory @inject */
	public $projectFormFactory;

	/** @var ProjectRepository @inject */
	public $projectRepository;

	/**
	 * List all enabled projects
	 */
	public function actionDefault()
	{
		$this->template->projects = $this->projectRepository->findAll();
	}

	/**
	 * Edit project by id
	 * @param int $id
	 * @throws BadRequestException
	 */
	public function actionEdit($id)
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			throw new BadRequestException;
		}

		$this->getComponent('projectForm')->setProject($project);
		$this->template->project = $project;
	}

	/**
	 * Enable project to do auto merge
	 * @param int $gitlabId
	 * @throws BadRequestException
	 */
	public function actionEnable($gitlabId)
	{
		$project = $this->gitlabClient->api('projects')->show($gitlabId);
		if (!isset($project['id'])) {
			throw new BadRequestException;
		}

		$this->getComponent('projectForm')->setGitlabData($project['id'], $project['name_with_namespace'], $project['web_url']);
		$this->template->gitlabId = $project['id'];
	}

	/**
	 * Search projects at gitlab
	 * @param string $text
	 */
	public function actionSearch($text)
	{
		$enabledProjects = [];
		foreach ($this->projectRepository->findAll() as $enabledProjectsRecord) {
			$enabledProjects[$enabledProjectsRecord->gitlab_id] = $enabledProjectsRecord;
		}

		$this->template->enabledProjects = $enabledProjects;
		$this->template->search = $text;
		$this->template->projects = $this->gitlabClient->api('projects')->search($text);
	}

	/**
	 * Add webhook needed for auto merging
	 * @param int $id
	 */
	public function handleAddWebhook($id)
	{
		$webhookUrl = $this->settingsRepository->getGitlabMergerUrl()
			? $this->settingsRepository->getGitlabMergerUrl() . '/webhook'
			: $this->link('//Webhook:default');

		// @TODO: when gitLab api will return note_events in webhook definition check for duplicates
		$result = $this->gitlabClient->api('projects')->addHook($id, $webhookUrl, [
			'note_events' => true,
			'push_events' => false,
			'issues_events' => false,
			'merge_requests_events' => false,
			'tag_push_events' => false
		]);

		isset($result['id'])
			? $this->flashMessage('Webhook successfully added', 'success')
			: $this->flashMessage('Failed to add webhook', 'danger');

		$this->redirect('this');
	}

	/**
	 * @return ProjectForm
	 */
	public function createComponentProjectForm()
	{
		$form = $this->projectFormFactory->create();
		$form->onSuccess[] = function() {
			$this->flashMessage('Project settings saved', 'success');
			$this->redirect('default');
		};
		return $form;
	}
}
