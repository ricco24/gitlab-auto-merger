<?php

namespace App\Presenters;

use App\Components\Menu\Menu;
use App\Model\Repository\SettingsRepository;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    /** @var SettingsRepository @inject */
    public $settingsRepository;

    /**
     * Base startup for all presenters
     */
    public function startup()
    {
        parent::startup();
        if (!$this->settingsRepository->isConfigured() && $this->name != 'Settings') {
            $this->redirect('Settings:default');
        }
    }

    /**
     * Top menu component
     * @return Menu
     */
    protected function createComponentMenu()
    {
        return new Menu();
    }
}
