<?php
namespace MacFJA\PHPQAExtensions\Tools\Analyzer;

use MacFJA\PHPQAExtensions\ToolDefinition;

/**
 * PHPQA tool mapping for PhpAssumptions
 *
 * @author  MacFJA
 * @license MIT
 */
class PhpAssumptions extends \Edge\QA\Tools\Tool implements ToolDefinition
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'xml' => ['phpa.xml'],
        'errorsXPath' => '//file/line',
        'composer' => 'rskuipers/php-assumptions',
        'internalClass' => 'PhpAssumptions\Analyser',
    );

    public function __invoke()
    {
        $args['format'] = 'pretty';
        if ($this->options->isSavedToFiles) {
            $args['format'] = 'xml';
            $args['output'] = $this->options->toFile('phpa.xml');
        }

        $analyzedDirs = $this->options->getAnalyzedDirs();
        $analyzedDir = reset($analyzedDirs);
        if (count($analyzedDirs) > 1) {
            $this->writeln("<error>PhpAssumptions analyzes only first directory {$analyzedDir}</error>");
        }

        $args['exclude'] = implode(',', $this->options->ignore->phpstan());
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
        return ltrim(substr(static::class, strrpos(static::class, '\\')), '\\');
    }

    /**
     * Get tool report xsl filename (or {@code null} for cli)
     *
     * @return string|null
     */
    public static function getReportName()
    {
        return __DIR__ . '/../../../app/report/phpassumptions.xsl';
    }

    /**
     * Get tool cli name
     *
     * @return string
     */
    public static function getCliName()
    {
        return 'phpa';
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
