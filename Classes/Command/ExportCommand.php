<?php

namespace Cron\AclsFromHell\Command;

use Cron\AclsFromHell\Domain\Model\BeGroup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExportCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $io = null;

    /**
     * @var []
     */
    protected $conf = null;

    /**
     * Configure the command by defining the name
     */
    protected function configure()
    {
        $this->setDescription('Exports on BE group into a yaml file');

        $this->addArgument(
            'group',
            InputArgument::REQUIRED, // OPTIONAL, REQUIRED, IS_ARRAY
            'Id of BE group to be exported'
        );

        $this->addOption(
            'dry-run',
            'd',
            InputOption::VALUE_NONE,
            'Dry-Run?'
        );
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // For more styles/helpers see: https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);

        if ($output->isVerbose()) {
            $this->io->title($this->getDescription());
        }


        $this->dryRun = (bool)$input->getOption('dry-run');
        $groupUid = (int)$input->getArgument('group');

        $group = $this->getGroupRecord($groupUid);

        if (empty($group)) {
            $this->io->error('Failure! Couldn\'t find group with uid ' . $groupUid);
            return Command::FAILURE;
        }

        $configPath = BeGroup::getConfigPath();

        if (!is_dir($configPath)) {
            GeneralUtility::mkdir($configPath);
        }

        $yamlFileName = preg_replace('/([^a-z0-9]+)/', '-', strtolower($group['title']));
        $yamlFileName = $configPath . DIRECTORY_SEPARATOR . $yamlFileName . '.yaml';

        $yamlConfiguration = [];
        foreach (BeGroup::ALLOWED_FIELDS as $fieldName) {
            $yamlConfiguration[$fieldName] = GeneralUtility::trimExplode(',', $group[$fieldName], true);
        }

        $yamlFileContents = Yaml::dump($yamlConfiguration, 99, 2);

        if ($output->isVerbose()) {
            $this->io->writeln($yamlFileContents);
        }

        if ($this->dryRun) {
            $this->io->warning('Skipped exporting due to dry-run mode!');
        } else {
            if (GeneralUtility::writeFile($yamlFileName, $yamlFileContents)) {
                $yamlFileName = basename($yamlFileName);
                $this->io->success(sprintf(
                    'Successfully exported ACLs of group %d ("%s") to %s',
                    $groupUid,
                    $group['title'],
                    $yamlFileName
                ));
                $this->updateGroupRecord($groupUid, $yamlFileName);
                $this->io->success(sprintf(
                    'Linked group %d to file %s',
                    $groupUid,
                    $yamlFileName
                ));
            } else {
                $this->io->error(sprintf(
                    'Failure! Couldn\'t export ACLs of group %d ("%s") to %s',
                    $groupUid,
                    $group['title'],
                    $yamlFileName
                ));
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    protected function getGroupRecord($groupUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $rows = $queryBuilder
            ->select('*')
            ->from('be_groups')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($groupUid, \PDO::PARAM_INT)))
            ->execute()
            ->fetch();

        return $rows;
    }

    protected function updateGroupRecord($groupUid, $file)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder
            ->update('be_groups')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($groupUid, \PDO::PARAM_INT))
            )
            ->set('tx_aclsfromhell_file', $file);

        foreach (BeGroup::ALLOWED_FIELDS as $fieldName) {
            $queryBuilder->set($fieldName, null);
        }

        $queryBuilder->execute();
    }
}
