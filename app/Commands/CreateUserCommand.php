<?php


namespace App\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-user';

    protected function configure()
    {
        $this
            ->setDescription('Crea un nuevo usuario.')
            ->setHelp('Este comando sirve para crear un nuevo usuario desde el servidor')
            ->addArgument('email', InputArgument::REQUIRED, 'Email del usuario');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hola Luis su correo es: ' . $input->getArgument('email'));
    }
}