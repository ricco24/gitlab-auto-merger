<?php

namespace App\Components\ProjectForm;

use App\Model\Repository\ProjectRepository;

class ProjectFormFactory
{
	/** @var ProjectRepository */
	protected $projectRepository;

	/**
	 * @param ProjectRepository $projectRepository
	 */
	public function __construct(ProjectRepository $projectRepository)
	{
		$this->projectRepository = $projectRepository;
	}

	/**
	 * @return ProjectForm
	 */
	public function create()
	{
		$form = new ProjectForm($this->projectRepository);
		return $form;
	}
}