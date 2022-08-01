<?php

namespace Cron\AclsFromFiles;

use TYPO3\CMS\Core\Core\Environment;

class TceMain
{
    public function renderFiles(array &$config)
    {
        if (empty($config['items'])) {
            $config['items'][] = ['', null];

            $configPath = Environment::getConfigPath() . DIRECTORY_SEPARATOR . 'acls';

            if (is_dir($configPath)) {
                $files = glob($configPath . DIRECTORY_SEPARATOR . '*.yaml');
                foreach ($files as $file) {
                    $config['items'][] = [
                        basename($file),
                        $file,
                    ];
                }
            }
        }
    }
}
