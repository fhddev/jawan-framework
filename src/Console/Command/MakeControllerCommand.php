<?php
namespace Jawan\Console\Command;

use Jawan\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';

    protected $controllersDirectory = ROOT . '/App/Controllers/';

    protected function configure()
    {
        $this
        ->setDescription('Create a new controller.')
        ->setHelp('This command allows you to create new Jawan Controller.')
        ->addArgument('Controller Name', InputArgument::REQUIRED, 'The controller name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating new Jawan Controller...');

        $inputControllerName = $this->parseControllerName($input->getArgument('Controller Name'));

        //
        if ( ! is_dir($this->getDirectory())) 
        {
            $output->writeln('Controllers directory does not exists in ' . $this->getDirectory());
            
            return Command::FAILURE;
        }

        // 
        if (file_exists($this->getFullPath($inputControllerName)))
        {
            $output->writeln($inputControllerName . ' is already exists.');
            
            return Command::FAILURE;
        }

        $stubContent = file_get_contents(__DIR__.'/../Stub/Controller.stub');

        $parsedStubContent = str_replace(
            [ 'stub.namespace', 'stub.class-name-lc', 'stub.class-name' ], 
            [ 'App\Controllers', strtolower($inputControllerName), $inputControllerName], 
            $stubContent);

        if (file_put_contents($this->getFullPath($inputControllerName), $parsedStubContent) !== FALSE)
        {
            $output->writeln('Controller created successfully');
            
            return Command::SUCCESS;
        }
        else
        {
            $output->writeln('Error: Something went wrong!');

            return Command::FAILURE;
        }
    }

    protected function parseControllerName($controllerName)
    {
        return $controllerName;
    }

    protected function getFullPath($fileName) 
    {
        return $this->getDirectory() . $fileName . '.php';
    }

    public function getDirectory() 
    {
        return $this->controllersDirectory;
    }
    
}
