<?php

defined('TYPO3') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauthgroup.php']['fetchGroups_postProcessing']['loadAclsFromHell'] = \Cron\AclsFromHell\Hooks\BackendUserAuthentication::class . '->loadAclsFromHell';
