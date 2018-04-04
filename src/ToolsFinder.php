<?php
namespace MacFJA\PHPQAExtensions;

/**
 * Tools finder
 *
 * @author  MacFJA
 * @license MIT
 */
class ToolsFinder
{
    /** @var array<string,string> */
    protected $available = [];

    /**
     * Get the list of tool available. (Tools implemented)
     *
     * @return array<string,string>
     */
    public function getAvailableTools()
    {
        if (count($this->available) === 0) {
            // include all tools
            $before = get_declared_classes();
            $files = glob(__DIR__ . '/Tools/Analyzer/*.php');
            foreach ($files as $file) {
                include_once $file;
            }
            $after = get_declared_classes();
            $this->available = [];
            foreach (array_diff($after, $before) as $class) {
                if (!in_array(ToolDefinition::class, class_implements($class), true)) {
                    continue;
                }

                $this->available[] = [
                    'name'      => call_user_func([$class, 'getToolName']),
                    'cli'       => call_user_func([$class, 'getCliName']),
                    'classname' => $class,
                    'composer'  => call_user_func([$class, 'getComposer']),
                    'internalClass'  => call_user_func([$class, 'getInternalClass'])
                ];
            }
        }

        return $this->available;
    }

    /**
     * Find a tool base on:
     *
     *  - its name
     *  - its cli file name
     *  - composer package
     *
     * @param string $data
     * @return array|null
     */
    public function findToolFrom($data)
    {
        $tools = $this->getAvailableTools();
        foreach ($tools as $tool) {
            if (in_array($data, $tool, true)) {
                return $tool;
            }
        }
        return null;
    }

    /**
     * Indicate if a tool is installed (found with the autoloader) or not
     *
     * @param array $tool
     * @return bool
     */
    public function isToolInstalled($tool)
    {
        return class_exists($tool['internalClass']);
    }
}
