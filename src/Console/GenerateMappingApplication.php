<?php namespace Vdbf\SiteMapper\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class GenerateMappingApplication extends Application
{

    protected function getCommandName(InputInterface $input)
    {
        // This should return the name of your command.
        return 'sitemap:generate';
    }

    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new GenerateMappingCommand();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}