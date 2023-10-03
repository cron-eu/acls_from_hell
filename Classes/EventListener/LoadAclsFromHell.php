<?php

declare(strict_types=1);

namespace Cron\AclsFromHell\EventListener;

use Cron\AclsFromHell\Domain\Model\BeGroup;
use TYPO3\CMS\Core\Authentication\Event\AfterGroupsResolvedEvent;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LoadAclsFromHell
{
    /**
     * This function resolves file reference of be_groups to external yaml files
     * that contain (additional) values for a bunch of ACL fields of that be_group.
     *
     * Keep in mind: this is not an override mechanism but an addition of
     * comma separated values!
     *
     * @param  AfterGroupsResolvedEvent $event
     * @return void
     */
    public function __invoke(AfterGroupsResolvedEvent $event): void
    {
        if ($event->getSourceDatabaseTable() !== 'be_groups') {
            // we are only interested in backend users
            return;
        }

        $yamlLoader = GeneralUtility::makeInstance(YamlFileLoader::class);

        $groups = $event->getGroups();

        // Iterate over all groups that have been loaded
        foreach ($groups as &$group) {
            // In case they've got an external file
            if ($group['tx_aclsfromhell_file']) {
                $filepath = BeGroup::getConfigPath() . DIRECTORY_SEPARATOR . basename($group['tx_aclsfromhell_file']);

                // Load that file
                if ($groupAcl = $yamlLoader->load($filepath)) {
                    // Iterate over its config
                    foreach ($groupAcl as $fieldName => $fieldConfig) {
                        // Check if fieldName is on list of fields that are allowed to be extended
                        if (in_array($fieldName, BeGroup::ALLOWED_FIELDS)) {
                            $group[$fieldName] .= join(',', $fieldConfig);
                        }
                    }
                }
            }
        }

        $event->setGroups($groups);
    }
}
