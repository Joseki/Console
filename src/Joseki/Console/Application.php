<?php

namespace Joseki\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends \Symfony\Component\Console\Application
{

    public function __construct()
    {
        parent::__construct('Joseki/Console by Miroslav Paulik.');
    }



    /**
     * Runs the current application.
     *
     * Always show the version information except when the user invokes the help
     * command as that already does it
     *
     * @param InputInterface $input An Input instance
     * @param OutputInterface $output An Output instance
     * @return integer 0 if everything went fine, or an error code otherwise
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasParameterOption(array('--help', '-h')) === false && $input->getFirstArgument() !== null) {
            $output->writeln($this->getLongVersion());
            $output->writeln('');
        }

        return parent::doRun($input, $output);
    }



    public function getLongVersion()
    {
        $name = $this->getName();
        if ($name === 'UNKNOWN') {
            $name = 'Console Tool';
        }

        return sprintf('<info>%s</info>', $name);
    }
} 
