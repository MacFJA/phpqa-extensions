<?php
namespace MacFJA\PHPQAExtensions;

use Symfony\Component\Yaml\Yaml;
use Wikimedia\RelPath;

/**
 * Installer
 *
 * @author  MacFJA
 * @license MIT
 */
class ToolInstaller
{
    /**
     * Install a tool.
     *
     * Read all needed information from the Tools\Analyser class and run composer
     *
     * @param string $class The Tools\Analyser class name
     */
    public function install($class)
    {
        if (!in_array(ToolDefinition::class, class_implements($class), true)) {
            throw new \InvalidArgumentException;
        }
        
        $this->runComposer(call_user_func([$class, 'getComposer']));
        $this->updatePhpQaConfig(
            call_user_func([$class, 'getCliName']),
            $class,
            call_user_func([$class, 'getReportName'])
        );
    }

    /**
     * Run a command `composer require --dev`
     * (Use the php function `exec`)
     *
     * @param string $packageName
     */
    public function runComposer($packageName)
    {
        exec('composer require --dev '.$packageName);
    }

    /**
     * Update the phpqa configuration file in the **Current Working Directory** to add a new tool
     *
     * @param string      $toolName
     * @param string      $toolClass
     * @param string|null $toolReport
     */
    public function updatePhpQaConfig($toolName, $toolClass, $toolReport = null)
    {
        $configPath = getcwd().'/.phpqa.yml';
        $config = [];

        if (file_exists($configPath)) {
            $config = Yaml::parse(file_get_contents($configPath));
        }

        // Add tools
        if (!array_key_exists('tool', $config)) {
            $config['tool'] = [];
        }
        $config['tool'][$toolName] = $toolClass;
        

        if (is_string($toolReport)) {
            // Add Report
            if (!array_key_exists('report', $config)) {
                $config['report'] = [];
            }
            $config['report'][$toolName] = RelPath::getRelativePath(realpath($toolReport), getcwd());
        }
        
        file_put_contents($configPath, Yaml::dump($config));
    }
}
