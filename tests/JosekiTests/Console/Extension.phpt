<?php

/**
 * Test: Joseki\Console\Extension.
 *
 * @testCase Joseki\Console\ExtensionTest
 * @author Miroslav Paul�k <miras.paulik@seznam.cz>
 */

namespace JosekiTests\Console;

use Joseki;
use Nette;
use Symfony\Component\Console\Command\Command;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class CommandMock1 extends Command
{

	protected function configure()
	{
		$this->setName('test:mock1')->setDescription('Simple first command');
	}

}

class CommandMock2 extends Command
{

	protected function configure()
	{
		$this->setName('test:mock2')->setDescription('Simple second command');
	}

}

class ExtensionTest extends Tester\TestCase
{

	private function prepareConfigurator()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(array('container' => array('class' => 'SystemContainer_' . Nette\Utils\Random::generate())));
		Joseki\Console\DI\ConsoleExtension::register($config);

		return $config;
	}

	public function testCommands()
	{
		$config = $this->prepareConfigurator();
		$config->addConfig(__DIR__ . '/data/config.commands.neon', Nette\Configurator::NONE);

		/** @var \Nette\DI\Container $container */
		$container = $config->createContainer();

		/** @var Joseki\Console\Application $cli */
		$cli = $container->getService('console.cli');

		Assert::true($cli instanceof Joseki\Console\Application);
		Assert::equal(2, count($cli->all('test')));
	}

	public function testNoCommands()
	{
		$config = $this->prepareConfigurator();

		/** @var \Nette\DI\Container $container */
		$container = $config->createContainer();

		/** @var Joseki\Console\Application $cli */
		$cli = $container->getService('console.cli');

		Assert::true($cli instanceof Joseki\Console\Application);
		Assert::equal(0, count($cli->all('test')));
	}

}

\run(new ExtensionTest());
