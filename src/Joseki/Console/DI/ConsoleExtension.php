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

    const TAG_JOSEKI_COMMAND = 'joseki.console.command';
    const TAG_KDYBY_COMMAND = 'kdyby.console.command';

    public $defaults = [
        'applicationClass' => 'Joseki\Console\Application',
        'commands' => [],
        'console' => [],
    ];

    /** @var ServiceDefinition[] */
    private $consoleDefinitions = [];



    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);
        $container->addDefinition($this->prefix('cli'))
            ->setClass($config['applicationClass']);

        Validators::assert($config['commands'], 'array');
        Validators::assert($config['console'], 'array');
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
            $def->addTag(self::TAG_JOSEKI_COMMAND);
            $def->addTag(self::TAG_KDYBY_COMMAND);
        }

        foreach ($config['console'] as $key => $prefix) {
            $this->consoleDefinitions[$key] = $container->addDefinition($this->prefix('console.' . $key))
                ->setClass('Joseki\Console\CleverApplication', [$prefix]);
        }
    }



    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();
        /** @var ServiceDefinition $cli */
        $cli = $container->getDefinition($this->prefix('cli'));
        $services = $container->findByTag(self::TAG_JOSEKI_COMMAND);
        foreach (array_keys($services) as $serviceName) {
            $cli->addSetup('add', ['@' . $serviceName]);
            foreach ($this->consoleDefinitions as $def) {
                $def->addSetup('add', ['@' . $serviceName]);
            }
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
