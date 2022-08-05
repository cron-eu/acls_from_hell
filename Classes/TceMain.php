<?php

namespace Cron\AclsFromHell;

use Cron\AclsFromHell\Domain\Model\BeGroup;

class TceMain
{
    public function renderFiles(array &$config)
    {
        if (empty($config['items'])) {
            $config['items'][] = ['', null];

            $configPath = BeGroup::getConfigPath();

            if (is_dir($configPath)) {
                $files = glob($configPath . DIRECTORY_SEPARATOR . '*.yaml');
                foreach ($files as $file) {
                    $config['items'][] = [
                        basename($file),
                        basename($file),
                    ];
                }
            }
        }
    }
}
