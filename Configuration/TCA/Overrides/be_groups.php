<?php

defined('TYPO3') || die('Access denied.');

$GLOBALS['TCA']['be_groups']['columns']['tx_aclsfromfiles_file'] = [
    'label' => 'Load ACL from file',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'itemsProcFunc' => Cron\AclsFromFiles\TceMain::class . '->renderFiles',
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_groups',
    'tx_aclsfromfiles_file',
    '',
    'before:groupMods'
);
