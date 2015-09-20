<?php

namespace App\Model\Repository;

class ProjectRepository extends AbstractRepository
{
	/** @var string	*/
	protected $table = 'project';

	/**
	 * @param int $id
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function findByGitlabId($id)
	{
		return $this->findAll()->where(['gitlab_id' => $id])->fetch();
	}
}
