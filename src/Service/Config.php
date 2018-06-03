<?php
namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class Config
{

    /**
     * Get marketplace fee percent for a level
     *
     * @param $level
     * @return mixed
     */
    public function getFee($level)
    {
        $yaml = Yaml::parseFile($_SERVER['DOCUMENT_ROOT'] . '/../config/market_config.yaml');
        return $yaml['config']['fee'][$level];
    }

    /**
     * Get maximum fee for a level
     *
     * @param $level
     * @return mixed
     */
    public function getMaxFee($level)
    {
        $yaml = Yaml::parseFile($_SERVER['DOCUMENT_ROOT'] . '/../config/market_config.yaml');
        return $yaml['config']['max'][$level];
    }
}
