<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\Table;
use App\Entity\User\User;

#[AsCommand(
    name: 'app:user:list',
    description: 'get list of users',
)]
class UserListCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'email of the user')
            ->addArgument('username', InputArgument::OPTIONAL, 'username of the user')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role of the user')
            ->addArgument('job', InputArgument::OPTIONAL, 'Job of the user')
            ->addArgument('company', InputArgument::OPTIONAL, 'Company name of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $query = $this->entityManager->getRepository(User::class)->createQueryBuilder('u');

        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');
        $job = $input->getArgument('job');
        $company = $input->getArgument('company');

        if ($email) {
            $query->andWhere('u.email = :email')
                  ->setParameter('email', $email);
        }

        if ($username) {
            $query->andWhere('u.username = :username')
                  ->setParameter('username', $username);
        }

        if ($role) {
            $query->andWhere('u.roles IN :role')
                  ->setParameter('role', $role);
        }

        if ($company) {
            $query->join('u.company', 'c')
                  ->andWhere('c.name = :company')
                  ->setParameter('company', $company);
        }

        $result = $query->getquery()->getArrayResult();

        $header = User::getParameters();

        $list = [];
        foreach ($result as $user) {
            $userInf = [];
            foreach ($user as $key => $value) {
                if (is_array($value)) {
                    $userInf[$key] = "[" . implode(',', $value) . "]";
                } else {
                    $userInf[$key] = $value;
                }
            }
            $list[] = $userInf;
        }
        
        $table = new Table($output);
        $table->setHeaders(['Id', 'Username', 'First Name', 'Last Name', 'Email', 'Job', 'Password', 'Headshot', 'Roles', 'isVerified']);
        $table->setRows($list);
        $table->render();

        $rows = [];

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
