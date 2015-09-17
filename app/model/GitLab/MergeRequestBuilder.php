<?php

namespace App\Model\GitLab;

class MergeRequestBuilder
{
	/** @var string */
	protected $privateToken;

	/** @var string|null */
	protected $emergencyMergeNote;

	/**
	 * @param string $privateToken
	 * @param string|null $emergencyMergeNote
	 */
	public function __construct($privateToken, $emergencyMergeNote = null)
	{
		$this->privateToken = $privateToken;
		$this->emergencyMergeNote = $emergencyMergeNote;
	}

	/**
	 * Create new merge request model
	 * @param array $apiMergeRequest
	 * @param array $payload
	 * @return MergeRequest
	 */
	public function create(array $apiMergeRequest, array $payload)
	{
		$mr = new MergeRequest($this->privateToken, $apiMergeRequest, $payload);
		if ($this->emergencyMergeNote) {
			$mr->setEmergencyMergeNote($this->emergencyMergeNote);
		}
		return $mr;
	}
}
