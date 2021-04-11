<?php
namespace Jawan\Console\Command;

use Jawan\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ServeCommand extends Command
{
    protected static $defaultName = 'serve';

    protected function configure()
    {
        $this
        ->setDescription('Run PHP Server.')
        ->setHelp('This command will run PHP Development Server.')
        ->addArgument('Port', InputArgument::OPTIONAL, 'Listening port');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getArgument('Port') ? $input->getArgument('Port') : '8000';

        exec("php -S 127.0.0.1:$port -t webroot/");

        return Command::SUCCESS;
    }

}
