<?php
namespace Jawan\Console\Command;

use Jawan\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MakeModelCommand extends Command
{
    protected static $defaultName = 'make:model';

    protected $modelsDirectory = ROOT . '/App/Models/';

    protected function configure()
    {
        $this
        ->setDescription('Create a new model.')
        ->setHelp('This command allows you to create new Jawan Model.')
        ->addArgument('Model Name', InputArgument::REQUIRED, 'The model name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating new Jawan Model...');

        $inputModelName = $this->parseModelName($input->getArgument('Model Name'));

        //
        if ( ! is_dir($this->getDirectory())) 
        {
            $output->writeln('Models directory does not exists in ' . $this->getDirectory());
            
            return Command::FAILURE;
        }

        // 
        if (file_exists($this->getFullPath($inputModelName)))
        {
            $output->writeln($inputModelName . ' is already exists.');
            
            return Command::FAILURE;
        }

        $stubContent = file_get_contents(__DIR__.'/../Stub/Model.stub');

        $parsedStubContent = str_replace(
            [ 'stub.namespace', 'stub.class-name-lc', 'stub.class-name' ], 
            [ 'App\Models', strtolower($inputModelName), $inputModelName], 
            $stubContent);

        if (file_put_contents($this->getFullPath($inputModelName), $parsedStubContent) !== FALSE)
        {
            $output->writeln('Model created successfully');
            
            return Command::SUCCESS;
        }
        else
        {
            $output->writeln('Error: Something went wrong!');

            return Command::FAILURE;
        }
    }

    protected function parseModelName($modelName)
    {
        return $modelName;
    }

    protected function getFullPath($fileName) 
    {
        return $this->getDirectory() . $fileName . '.php';
    }

    public function getDirectory() 
    {
        return $this->modelsDirectory;
    }
}
