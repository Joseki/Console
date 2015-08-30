<?php

namespace Joseki\Console\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Nette\Utils\Validators;

class ConsoleExtension extends CompilerExtension
{

	const TAG_COMMAND = 'joseki.console.command';

	public $defaults = [
		'applicationClass' => 'Joseki\Console\Application',
		'commands' => [],
	];

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		$container->addDefinition($this->prefix('cli'))
			->setClass($config['applicationClass'], [$config]);

		Validators::assert($config['commands'], 'array');
		foreach ($config['commands'] as $key => $command) {
			$def = $container->addDefinition($this->prefix('command.' . $key));
			list($def->factory) = Compiler::filterArguments(
				[is_string($command) ? new Statement($command) : $command]
			);
			if (class_exists($def->factory->entity)) {
				$def->class = $def->factory->entity;
			}
			$def->setAutowired(false);
			$def->setInject(false);
			$def->addTag(self::TAG_COMMAND);
		}
	}

	public function beforeCompile()
	{
		$container = $this->getContainerBuilder();
		/** @var ServiceDefinition $cli */
		$cli = $container->getDefinition($this->prefix('cli'));
		$services = $container->findByTag(self::TAG_COMMAND);
		foreach (array_keys($services) as $serviceName) {
			$cli->addSetup('add', ['@' . $serviceName]);
		}
	}

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('console', new ConsoleExtension());
		};
	}

}
