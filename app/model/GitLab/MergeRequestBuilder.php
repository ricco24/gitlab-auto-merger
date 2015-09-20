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
	 */
	public function __construct($privateToken)
	{
		$this->privateToken = $privateToken;
	}

	/**
	 * Create new merge request model
	 * @param array $apiMergeRequest
	 * @param array $payload
	 * @return MergeRequest
	 */
	public function create(array $apiMergeRequest, array $payload)
	{
		return new MergeRequest($this->privateToken, $apiMergeRequest, $payload);
	}
}
