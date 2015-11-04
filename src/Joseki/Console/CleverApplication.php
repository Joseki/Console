<?php

namespace Joseki\Console;

use Symfony\Component\Console\Command\Command;

class CleverApplication extends Application
{

    /** @var string */
    private $prefix;



    public function __construct($prefix)
    {
        parent::__construct();
        $this->prefix = $prefix;
    }



    public function add(Command $command)
    {
        $name = $command->getName();
        if (substr($name, 0, strlen($this->prefix)) === $this->prefix) {
            $name = trim(substr($name, strlen($this->prefix)), ':');
            $command = clone $command;
            $command->setName($name);
            parent::add($command);
        } elseif (in_array($name, ['list', 'help'])) {
            parent::add($command);
        }
    }

}
