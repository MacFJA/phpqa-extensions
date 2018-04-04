<?php
namespace MacFJA\PHPQAExtensions\Tools\Analyzer;

use MacFJA\PHPQAExtensions\ToolDefinition;

/**
 * PHPQA tool mapping for PhpMagicNumberDetector
 *
 * @author  MacFJA
 * @license MIT
 */
class PhpMagicNumber extends \Edge\QA\Tools\Tool implements ToolDefinition
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'xml' => ['phpmnd.xml'],
        'errorsXPath' => '//file/entry',
        'composer' => 'povils/phpmnd',
        'internalClass' => 'Povils\PHPMND\Console\Application',
    );

    public function __invoke()
    {
        $ignoredNumbers = $this->config->csv('phpmnd.ignore-numbers');
        $ignoredFuncs   = $this->config->csv('phpmnd.ignore-funcs');
        $ignoredStrings = $this->config->csv('phpmnd.ignore-strings');
        $enableStrings = $this->config->value('phpmnd.strings');

        $args = [
            'extensions' => 'all',
            'hint' => null,
            'progress' => null,
            'non-zero-exit-on-violation' => null,
            'verbose' => null
        ];

        if ($ignoredNumbers === true) {
            $args['ignore-numbers'] = $ignoredNumbers;
        }
        if ($ignoredFuncs === true) {
            $args['ignore-funcs'] = $ignoredFuncs;
        }
        if ($ignoredStrings === true) {
            $args['ignore-strings'] = $ignoredStrings;
        }
        if ($enableStrings === true) {
            $args['strings'] = null;
        }

        if ($this->options->isSavedToFiles) {
            $args['xml-output'] = $this->options->toFile('phpmnd.xml');
            unset($args['non-zero-exit-on-violation']);
        }

        $args[] = $this->options->ignore->bergmann();

        $analyzedDirs = $this->options->getAnalyzedDirs();
        $analyzedDir = reset($analyzedDirs);
        if (count($analyzedDirs) > 1) {
            $this->writeln("<error>phpmnd analyzes only first directory {$analyzedDir}</error>");
        }
        
        $args[] = $analyzedDir;

        return $args;
    }

    /**
     * Get composer vendor/package name
     *
     * @return string
     */
    public static function getComposer()
    {
        return static::$SETTINGS['composer'];
    }

    /**
     * Get tool name
     *
     * @return string
     */
    public static function getToolName()
    {
        return 'PHP Magic Number Detector';
    }

    /**
     * Get tool report xsl filename (or {@code null} for cli)
     *
     * @return string|null
     */
    public static function getReportName()
    {
        return __DIR__.'/../../../app/report/phpmagicnumber.xsl';
    }

    /**
     * Get tool cli name
     *
     * @return string
     */
    public static function getCliName()
    {
        return 'phpmnd';
    }

    /**
     * Get the FQCN of the tool
     * (use to check if the tool exist trough the autoloader)
     *
     * @return string
     */
    public static function getInternalClass()
    {
        return static::$SETTINGS['internalClass'];
    }
}
