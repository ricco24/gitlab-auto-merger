<?php

namespace App\Presenters;

use App\Model\Repository\ProjectRepository;
use Gitlab\Client;
use Nette\Application\BadRequestException;

class MergeRequestPresenter extends BasePresenter
{
	/** @var Client @inject */
	public $gitlabClient;

	/** @var ProjectRepository @inject */
	public $projectRepository;

	/**
	 * List all opened and visible merge requests
	 * @param int $id
	 * @throws BadRequestException
	 */
	public function actionDefault($id)
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			throw new BadRequestException;
		}

		$this->template->project = $project;
		$this->template->mergeRequests = $this->gitlabClient->api('mr')->opened($project->gitlab_id, 1, 9999);
	}

	/**
	 * Manual merge request accept
	 * @param int $projectId
	 * @param int $gitlabMergeRequestId
	 */
	public function handleAccept($projectId, $gitlabMergeRequestId)
	{
		$project = $this->projectRepository->find($projectId);
		if (!$project) {
			$this->flashMessage('This project is not enabled to do auto merge', 'danger');
			$this->redirect('default', $projectId);
		}

		$mergeRequest = $this->gitlabClient->api('mr')->show($project->gitlab_id, $gitlabMergeRequestId);
		if (!isset($mergeRequest['id'])) {
			$this->flashMessage('Cannot fetch merge request from GitLab', 'danger');
			$this->redirect('default', $projectId);
		}

		$mergeResult = $this->gitlabClient->api('mr')->merge($mergeRequest['project_id'], $mergeRequest['id'], 'Merged via browser');
		if (!isset($mergeResult['state']) || $mergeResult['state'] != 'merged') {
			$this->flashMessage('Merge was unsuccessful', 'danger');
			$this->redirect('default', $projectId);
		}

		if ($project->delete_source_branch) {
			$this->gitlabClient->api('repositories')->deleteBranch($mergeResult['project_id'], $mergeResult['source_branch']);
		}

		$this->flashMessage('Merge request accepted', 'success');
		$this->redirect('default', $projectId);
	}
}
