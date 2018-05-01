<?php
namespace MacFJA\PHPQAExtensions\Tools\Analyzer;

use Edge\QA\OutputMode;
use Edge\QA\Tools\Tool;
use MacFJA\PHPQAExtensions\ToolDefinition;

/**
 * PHPQA tool mapping for PhpCodeAnalyzer
 *
 * @author  MacFJA
 * @license MIT
 */
class PhpCodeAnalyzer extends Tool implements ToolDefinition
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::HANDLED_BY_TOOL,
        'xml' => ['php-code-analyzer.xml'],
        'composer' => 'wapmorgan/php-code-analyzer',
        'internalClass' => 'wapmorgan\PhpCodeAnalyzer\PhpCodeAnalyzer',
    );

    public function __invoke()
    {
        $args = [$this->options->getAnalyzedDirs(' ')];
        if ($this->options->isSavedToFiles) {
            $args['output'] = $this->options->rawFile('php-code-analyzer.xml');
        }

        return $args;
    }

    /**
     * Get composer vendor/package name
     *
     * @return string
     */
    public static function getComposer()
    {
        return 'wapmorgan/php-code-analyzer';
    }

    /**
     * Get the tool display name
     *
     * @return string
     */
    public static function getToolName()
    {
        return 'PhpCodeAnalyzer';
    }

    /**
     * Get tool report xsl filename (or {@code null} for cli)
     *
     * @return string|null
     */
    public static function getReportName()
    {
        return __DIR__.'/../../../app/report/php-code-analyzer.xsl';
    }

    /**
     * Get tool cli name
     *
     * @return string
     */
    public static function getCliName()
    {
        return 'phpca';
    }

    /**
     * Get the FQCN of the tool
     * (use to check if the tool exist trough the autoloader)
     *
     * @return string
     */
    public static function getInternalClass()
    {
        return 'wapmorgan\PhpCodeAnalyzer\PhpCodeAnalyzer';
    }
}
