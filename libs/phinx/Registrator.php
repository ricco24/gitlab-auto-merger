<?php

namespace Bin\Phinx;

use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Phinx\Config\Config;
use Symfony\Component\Console\Application;

class Registrator
{
	/** @var string                 Path to nette config.local.neon file */
	private $configsDir = '/../../app/config';

	/** @var array                  Define phinx commands with aliases */
	private $command = array(
		'\Phinx\Console\Command\Init' => 'init',
		'\Phinx\Console\Command\Create' => 'create',
		'\Phinx\Console\Command\Migrate' => 'migrate',
		'\Phinx\Console\Command\Rollback' => 'rollback',
		'\Phinx\Console\Command\Status' => 'status',
		'\Phinx\Console\Command\Test' => 'test'
	);

	/**
	 * @param Application $application
	 */
	public function __construct(Application $application)
	{
		$config = new Config($this->buildConfig(), __FILE__);

		// Register all commands
		foreach ($this->command as $class => $commandName) {
			$command = new $class;
			$command->setName($commandName);
			if (is_callable(array($command, 'setConfig'))) {
				$command->setConfig($config);
			}
			$application->add($command);
		}
	}

	/**
	 * Build phinx config from config.local.neon
	 * @return array
	 */
	private function buildConfig()
	{
		$configData = array(
			'paths' => array(
				'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
			),
			'environments' => array(
				'default_migration_table' => 'phinxlog',
				'default_database' => 'local',
			),
		);

		foreach (Finder::findFiles('config.*.neon')->in(__DIR__ . $this->configsDir) as $configFile) {
			$neon = Neon::decode(file_get_contents($configFile->getRealPath()));

			if ($neon) {
				$dbName = substr($configFile->getBaseName(), 7, -5);
				$dbData = $neon['parameters']['database']['default'];
				$configData['environments'][$dbName] = array(
					'adapter' => $dbData['adapter'],
					'host' => $dbData['host'],
					'name' => $dbData['dbname'],
					'user' => $dbData['user'],
					'pass' => $dbData['password'],
					'port' => 3306,
					'chaarset' => 'utf8'
				);
			}
		}
		return $configData;
	}
}