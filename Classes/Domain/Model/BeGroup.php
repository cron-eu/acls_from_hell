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
        'mfa_providers', // TYPO3 11+
    ];

    public static function getAllowedFields()
    {
        return array_filter(self::ALLOWED_FIELDS, function($fieldName) {
            return $GLOBALS['TCA']['be_groups']['columns'][$fieldName] ?? false;
        });
    }

    public static function getConfigPath()
    {
        return Environment::getConfigPath() . DIRECTORY_SEPARATOR . 'acls';
    }
}
