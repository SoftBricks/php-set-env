<?php

namespace SoftBricks\CLI;

/**
 * Class SetEnv
 * Helps setting params into .EVN files
 * @package SoftBricks\CLI
 */
class SetEnv extends ConsoleApp
{
    /**
     * Returns full filepath of target .env file
     *
     * @return string|null
     */
    private function getEnvFile()
    {
        foreach(scandir(getcwd()) as $file) {
            if (is_file($file) && strtolower($file) === '.env') {
                return $file;
            }
        }
        return null;
    }

    /**
     * Prints error message and quits script execution with error code
     *
     * @param $text
     */
    private function printError($text)
    {
        $this->_print($text);
        $this->println(' Failure', Color::$RED);
        exit(1);
    }

    /**
     * Prints success message and quites script execution with success code
     *
     * @param $text
     */
    private function printSuccess($text)
    {
        $this->_print($text);
        $this->println(' Ok', Color::$LIGHTGREEN);
        exit(0);
    }

    /**
     * Rewrites .env file with given config
     *
     * @param $config
     * @param $filePath
     * @return bool
     */
    private function writeEnvFile($envContent, $filePath)
    {
        $content = "";

        // we want a cleanly sorted env file in the end
        ksort($envContent);

        // we add configs without that are outside of categories first
        foreach($envContent as $confKey => $config) {
            if (!is_array($config)) {
                $content .= "${confKey}=${config}\n";
            }
        }

        // we add configs inside of categories second
        foreach($envContent as $confKey => $config) {
            if (is_array($config)) {
                $content .= "\n[${confKey}]\n";
                foreach ($config as $key => $value) {
                    $content .= "${key}=${value}\n";
                }
            }
        }
        return file_put_contents($filePath, $content) !== false;
    }

    protected function run()
    {
        // try to find ENV file in local directory
        $envFileName = $this->getEnvFile();
        $envPath = getcwd() . '/' . $envFileName;
        if ($envFileName === null) {
            $this->printError('Could not find .env file in current directory.');
        }

        // we analyze passed in arguments to check if we got everything we need
        $key = $this->getArg('--key');
        $value = $this->getArg('--value');
        $category = $this->getArg('--category');
        if ($key === null) $this->printError('no key argument provided');
        if ($value === null) $this->printError('no value argument provided');

        // we parse current .env file and add the config we need to add
        $envContent = parse_ini_file($envPath, true);
        if ($category !== null) {
            $envContent[$category][$key] = $value;
        } else {
            $envContent[$key] = $value;
        }

        // write back changed config
        if (!$this->writeEnvFile($envContent, $envPath)) {
            $this->printError('Could not update '.$envFileName);
        }

        // script executed successfully, so we are setting a success status
        $this->printSuccess($envFileName . ' updated');
    }
}