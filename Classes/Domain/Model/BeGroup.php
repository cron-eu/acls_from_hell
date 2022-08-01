<?php

namespace Cron\AclsFromFiles\Domain\Model;

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
}
