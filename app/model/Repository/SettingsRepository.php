<?php

namespace App\Model\Repository;

class SettingsRepository extends AbstractRepository
{
	/** @var string	*/
	protected $table = 'settings';

	/** @var array			Cached db data */
	protected $cache;

	/**
	 * Fetch live settings data from database
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function fetch()
	{
		return $this->findAll()->where('id', 1)->fetch();
	}

	/**
	 * Check if all what need is configured
	 * @return bool
	 */
	public function isConfigured()
	{
		$this->getData();
		return $this->cache && $this->cache->gitlab_url && $this->cache->token;
	}

	/**
	 * @return string|null
	 */
	public function getGitlabUrl()
	{
		$this->getData();
		return $this->cache ? $this->cache->gitlab_url : null;
	}

	/**
	 * @return string|null
	 */
	public function getGitlabMergerUrl()
	{
		$this->getData();
		return $this->cache ? $this->cache->gitlab_merger_url : null;
	}

	/**
	 * @return string|null
	 */
	public function getToken()
	{
		$this->getData();
		return $this->cache ? $this->cache->token : null;
	}

	/**
	 * Fetch data from database to array cache
	 */
	protected function getData()
	{
		if ($this->cache === null) {
			$this->cache = $this->fetch();
		}
	}
}
