<?php

namespace App\Model\GitLab;

class MergeRequest
{
	/** @var string */
	protected $privateToken;

	/** @var array 			Merge request array */
	protected $apiMergeRequest;

	/** @var array 			Full payload array */
	protected $payload;

	/** @var string|bool	Cached build status */
	protected $buildStatus;

	/** @var string */
	protected $emergencyMergeNote;

	/**
	 * @param string $privateToken
	 * @param array $apiMergeRequest
	 * @param array $payload
	 */
	public function __construct($privateToken, array $apiMergeRequest, array $payload)
	{
		$this->apiMergeRequest = $apiMergeRequest;
		$this->payload = $payload;
		$this->privateToken = $privateToken;
	}

	/**
	 * @param string $emergencyMergeNote
	 */
	public function setEmergencyMergeNote($emergencyMergeNote)
	{
		$this->emergencyMergeNote = $emergencyMergeNote;
	}

	/**
	 * Can be merge request merged?
	 * Check only merge request payload merge status
	 * @return bool
	 */
	public function canBeMerged()
	{
		return $this->payload['merge_request']['merge_status'] == 'can_be_merged';
	}

	/**
	 * Fetch build status for merge request
	 * @param bool|false $force			Force refetch build status
	 * @return bool|string
	 */
	public function buildStatus($force = false)
	{
		if ($this->buildStatus === null || $force) {
			$url = $this->payload['object_attributes']['url'];
			$buildStatusUrl = substr($url, 0, strpos($url, '#')) . '/ci_status?private_token=' . $this->privateToken;

			$ch = curl_init($buildStatusUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$response = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);

			if ($info['http_code'] != 200) {
				$this->buildStatus = false;
				return false;
			}

			$decoded = json_decode($response, true);
			if (!$decoded) {
				$this->buildStatus = false;
				return false;
			}

			if (!isset($decoded['status'])) {
				$this->buildStatus = false;
				return false;
			}

			$this->buildStatus = $decoded['status'];
		}

		return $this->buildStatus;
	}

	/**
	 * Is build success?
	 * @param bool|false $force
	 * @return bool
	 */
	public function isBuildSuccess($force = false)
	{
		$status = $this->buildStatus($force);
		return $status === 'success';
	}

	/**
	 * Is merge request marked as *work in progress*?
	 * @return bool
	 */
	public function isWorkInProgress()
	{
		$title = $this->apiMergeRequest['title'];
		return substr($title, 0, 5) == '[WIP]' || substr($title, 0, 3) == 'WIP';
	}

	/**
	 * Calculate up/down votes diff
	 * @return int
	 */
	public function getVotesDiff()
	{
		return (int) $this->apiMergeRequest['upvotes'] - (int) $this->apiMergeRequest['downwotes'];
	}

	/**
	 * Is merge request in opened state?
	 * @return bool
	 */
	public function isOpened()
	{
		return $this->apiMergeRequest['state'] == 'opened';
	}

	/**
	 * Wanna do emergency merge?
	 * @return bool
	 */
	public function needEmergencyMerge()
	{
		return $this->emergencyMergeNote && (strtolower($this->payload['object_attributes']) == $this->emergencyMergeNote);
	}

	/**
	 * Check if merge request can be automerged
	 * @param int $positiveVotesDiff
	 * @return bool
	 */
	public function canBeAutoMerged($positiveVotesDiff = 1)
	{
		return $this->isOpened()
			&& $this->canBeMerged()
			&& $this->isBuildSuccess()
			&& !$this->isWorkInProgress()
			&& ($this->getVotesDiff() >= $positiveVotesDiff);
	}

	/**
	 * Check if merge request can be emergency automerged
	 * Ignore all requirements but can be merged from payload and opened state
	 * @return bool
	 */
	public function canBeEmergencyAutoMerged()
	{
		return $this->isOpened() && $this->canBeMerged();
	}
}
