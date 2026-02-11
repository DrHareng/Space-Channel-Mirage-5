<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:promote',
    description: 'Promote an existing user by adding one or more roles (defaults to ROLE_ADMIN)',
)]
final class PromoteUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user to promote')
            ->addArgument(
                'roles',
                InputArgument::IS_ARRAY,
                'Roles to add (separate multiple roles with a space). Defaults to ROLE_ADMIN.',
                ['ROLE_ADMIN'],
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getArgument('email');
        /** @var list<string> $newRoles */
        $newRoles = (array) $input->getArgument('roles');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            $io->error(sprintf('User with email "%s" was not found.', $email));

            return Command::FAILURE;
        }

        $currentRoles = $user->getRoles();
        $mergedRoles = array_values(array_unique([...$currentRoles, ...$newRoles]));

        $user->setRoles($mergedRoles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf(
            'User "%s" promoted. Roles are now: %s',
            $email,
            implode(', ', $mergedRoles),
        ));

        return Command::SUCCESS;
    }
}

