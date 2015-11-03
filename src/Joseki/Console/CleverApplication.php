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



    public function find($name)
    {
        return parent::find(sprintf('%s:%s', $this->prefix, $name));
    }



    public function add(Command $command)
    {
        $name = $command->getName();
        if (substr($name, 0, strlen($this->prefix)) === $this->prefix) {
            parent::add($command);
        }
    }

}
