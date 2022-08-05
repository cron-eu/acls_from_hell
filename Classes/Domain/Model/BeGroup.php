<?php

namespace Cron\AclsFromHell\Domain\Model;

use TYPO3\CMS\Core\Core\Environment;

class BeGroup
{
    public const ALLOWED_FIELDS = [
        'non_exclude_fields',
        'explicit_allowdeny',
        'pagetypes_select',
        'tables_select',
        'tables_modify',
        'groupMods',
        'availableWidgets',
        'file_permissions',
    ];

    public static function getConfigPath()
    {
        return Environment::getConfigPath() . DIRECTORY_SEPARATOR . 'acls';
    }
}
