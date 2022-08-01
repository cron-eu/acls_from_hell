<?php

defined('TYPO3') || die('Access denied.');

$newField = 'tx_aclsfromfiles_file';

// Add new field 'file'
$GLOBALS['TCA']['be_groups']['columns'][$newField] = [
    'label' => 'Load ACL from file',
    'description' => 'When a file is selected here the ACLs will *NOT* come from DB but from said file!',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'itemsProcFunc' => Cron\AclsFromFiles\TceMain::class . '->renderFiles',
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_groups',
    $newField,
    '',
    'before:groupMods'
);

// Make other fields disappear when 'file' is set!
foreach (\Cron\AclsFromFiles\Domain\Model\BeGroup::ALLOWED_FIELDS as $fieldName) {
    $GLOBALS['TCA']['be_groups']['columns'][$fieldName]['displayCond'] = 'FIELD:' . $newField . ':REQ:false';
}
