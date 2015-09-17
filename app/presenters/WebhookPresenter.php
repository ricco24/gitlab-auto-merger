<?php

namespace App\Presenters;

use App\Model\GitLab\MergeRequestBuilder;
use Gitlab\Client;
use Nette\Http\Request;
use Tracy\Debugger;

class WebhookPresenter extends BasePresenter
{
	/** @var Client @inject */
	public $gitlabClient;

	/** @var Request @inject */
	public $httpRequest;

	/** @var MergeRequestBuilder @inject */
	public $mergeRequestBuilder;

	/**
	 * Auto merge
	 */
	public function actionDefault()
	{
		$payload = $this->httpRequest->getRawBody();
		if (!$payload) {
			Debugger::log('No payload data', Debugger::ERROR);
			$this->terminate();
		}

		$data = json_decode($payload, true);
		if (!$data) {
			Debugger::log('json_decode error', Debugger::ERROR);
			$this->terminate();
		}

		if ($data['object_kind'] != 'note') {
			Debugger::log('Only notes object kind processing now. *' . $data['object_kind'] . '* given', Debugger::ERROR);
			$this->terminate();
		}

		$projectId = isset($data['merge_request']['source_project_id']) ? $data['merge_request']['source_project_id'] : false;
		$mergeRequestId = isset($data['merge_request']['id']) ? $data['merge_request']['id'] : false;

		if (!$projectId || !$mergeRequestId) {
			Debugger::log('projectId or mergeRequestId missing', Debugger::ERROR);
			$this->terminate();
		}

		$mr = $this->gitlabClient->api('mr')->show($projectId, $mergeRequestId);
		$mergeRequest = $this->mergeRequestBuilder->create($mr, $data);

		if ($mergeRequest->needEmergencyMerge() && $mergeRequest->canBeEmergencyAutoMerged()) {
			$this->gitlabClient->api('mr')->merge($projectId, $mergeRequestId, 'Emergency auto merged');
		}

		if ($mergeRequest->canBeAutoMerged($this->context['mergeRequests']['positiveVotesDiff'])) {
			$this->gitlabClient->api('mr')->merge($projectId, $mergeRequestId, 'Auto merged');
		}
	}
}
