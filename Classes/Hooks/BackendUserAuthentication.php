<?php

namespace Cron\AclsFromFiles\Hooks;

use Cron\AclsFromFiles\Domain\Model\BeGroup;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserAuthentication
{
    /**
     * This function resolves file reference of be_groups to external yaml files
     * that contain (additional) values for a bunch of ACL fields of that be_group.
     *
     * Keep in mind: this is not an override mechanism but an addition of
     * comma separated values!
     *
     * @param  array $params
     * @param  \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $caller
     * @return void
     */
    public function loadAclsFromFiles($params, $caller)
    {
        $yamlLoader = GeneralUtility::makeInstance(YamlFileLoader::class);

        // Iterate over all groups that have been loaded
        foreach ($caller->userGroups as $group) {
            // In case they've got an external file
            if ($group['tx_aclsfromfiles_file']) {
                $filepath = BeGroup::getConfigPath() . DIRECTORY_SEPARATOR . basename($group['tx_aclsfromfiles_file']);

                // Load that file
                if ($groupAcl = $yamlLoader->load($filepath)) {
                    // Iterate over its config
                    foreach ($groupAcl as $fieldName => $fieldConfig) {
                        // Check if fieldName is on list of fields that are allowed to be extended
                        if (in_array($fieldName, BeGroup::ALLOWED_FIELDS)) {
                            // Add config to pre-built field config
                            switch ($fieldName) {
                                case 'groupMods':
                                    $caller->dataLists['modList'] .= ',' . join(',', $fieldConfig);
                                    break;
                                case 'availableWidgets':
                                    $caller->dataLists['available_widgets'] .= ',' . join(',', $fieldConfig);
                                    break;
                                default:
                                    $caller->dataLists[$fieldName] .= ',' . join(',', $fieldConfig);
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
}
