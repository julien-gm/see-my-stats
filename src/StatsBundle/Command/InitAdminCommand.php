<?php

namespace StatsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class InitAdminCommand extends ContainerAwareCommand
{

    /**
     *
     */
    public function configure()
    {
        $this
            ->setName('stats:create-admin')
            ->addArgument('username', InputArgument::REQUIRED, 'The name of the administrator')
            ->setDescription('Creates the website administrator.')
            ->setHelp("This command allows you to create the website administrator...");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Admin Creator',
            '============',
            '',
        ]);

        $username = $input->getArgument('username');
        $helper = $this->getHelper('question');
        $question = new Question('What will be the password for ' . $username . '?');

        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);

        $confirmation = new Question('Please confirm:');
        $confirmation->setHidden(true);
        $confirmation->setHiddenFallback(false);

        $passwordConfirm = $helper->ask($input, $output, $confirmation);

        if ($password !== $passwordConfirm) {
            throw new \Exception('Passwords are not the same');
        }

        $email = new Question('What is your email?');
        $email = $helper->ask($input, $output, $email);

        $userManager = $this->getContainer()->get('stats.user_manager');
        $userManager->createAdmin($username, $email, $password);

        $output->writeln('User successfully generated!');
    }
}
