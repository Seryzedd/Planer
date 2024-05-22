<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $encoder;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'User name')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password')
            ->addArgument('email', InputArgument::OPTIONAL, 'User password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();

        $name = $input->getArgument('name') ?: 'root';
        $role = $input->getArgument('role') ?: 'ROLE_USER';
        $password = $input->getArgument('password') ?: 'a!164$da';
        $email = $input->getArgument('email') ?: 'contact@root_email.fr';

        $encoded = $this->encoder->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setUserName($name);
        $user->addRole($role);
        $user->setEmail($email);

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $io->success('User created.');

        return Command::SUCCESS;
    }
}
