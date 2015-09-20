<?php

namespace App\Components\SettingsForm;

use App\Model\Repository\SettingsRepository;

class SettingsFormFactory
{
	/** @var SettingsRepository */
	protected $settingsRepository;

	/**
	 * @param SettingsRepository $settingsRepository
	 */
	public function __construct(SettingsRepository $settingsRepository)
	{
		$this->settingsRepository = $settingsRepository;
	}

	/**
	 * @return SettingsForm
	 */
	public function create()
	{
		return new SettingsForm($this->settingsRepository);
	}
}