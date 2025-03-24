<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Throwable\LoadingThrowable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:set-admin-data',
    description: 'add/update default data in table use by the app',
)]
class SetDefaultDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:set-admin-data')
            ->setDescription('add/update default data in table use by the app')
            ->addArgument(
                'className',
                InputOption::VALUE_REQUIRED,
                'The class(es) of the entity to use'
            )
            ->addOption(
                'truncate-only',
                null,
                InputOption::VALUE_NONE,
                'If set, will only truncate the table without inserting data'
            )
            ->addArgument(
                'dataPath',
                InputOption::VALUE_REQUIRED,
                'The path(s) of the file(s) containing the default data'
            )
            ->setHelp('add/update default data in table use by the app'.PHP_EOL
                .'Usage is to indicate the className and the path'.PHP_EOL
                .'php bin/console app:set-default-data App\Entity\User \DataFixtures\Fixtures\user.yml');
    }

    /**
     * src/DataFixtures/Fixtures and persist all
     * @throws Exception|LoadingThrowable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /* Demande de confirmation avant d'exécuter la commande */

        /*if (!$io->confirm('Êtes-vous sûr de vouloir changer les données de base ?', false)) {
            $io->warning('L\'opération a été annulée.');
            return Command::SUCCESS;
        }*/

        $io->note('Loading...');
        $className = $input->getArgument('className');
        $dataPath  = dirname(__DIR__, 1) .$input->getArgument('dataPath');

        // Option: Truncate uniquement, sans réinsérer de données
        if ($input->getOption('truncate-only')) {
            $this->truncateTable($className);
            return Command::SUCCESS;
        }


        /**
         * verify path exist
         */
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($dataPath)) {
            throw new InvalidArgumentException(sprintf('The data file "%s" does not exist.', $dataPath));
        }
        $this->truncateTable($className);
        $this->loadData($dataPath);
        /**
         * flush all objects to database
         */
        $this->em->flush();
        $io->success(sprintf('Default data for "%s" loaded successfully.', $className));
        return Command::SUCCESS;
    }

    /**
     * truncate table
     * @param $className
     * @return void
     * @throws Exception
     */
    private function truncateTable($className): void
    {
        $connection = $this->em->getConnection();

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $platform  = $connection->getDatabasePlatform();
        $tableName = $this->em->getClassMetadata($className)->getTableName();
        $sql = $platform->getTruncateTableSQL($tableName, true /* whether to cascade */);
        $connection->executeStatement($sql);
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * load file to get default data
     * @throws LoadingThrowable
     */
    private function loadData($dataPath): void
    {
        $loader = new NativeLoader();
        $objects = $loader->loadFile($dataPath)->getObjects();

        foreach ($objects as $object) {
            if ($object instanceof User) {
                $hashedPassword = $this->passwordHasher->hashPassword($object, $object->getPassword());
                $object->setPassword($hashedPassword);
            }

            $this->em->persist($object);
        }
    }
}
