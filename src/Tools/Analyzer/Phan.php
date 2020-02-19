<?php
namespace MacFJA\PHPQAExtensions\Tools\Analyzer;

use Edge\QA\OutputMode;
use Edge\QA\Tools\Tool;
use MacFJA\PHPQAExtensions\ToolDefinition;

/**
 * PHPQA tool mapping for Phan
 *
 * @author  MacFJA
 * @license MIT
 */
class Phan extends Tool implements ToolDefinition
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::HANDLED_BY_TOOL,
        'xml' => ['phan.xml'],
        'errorsXPath' => [
            # ignoreWarnings => xpath
            false => '//checkstyle/file/error',
            true => '//checkstyle/file/error[@severity="error"]',
        ],
        'composer' => 'phan/phan',
        'internalClass' => '\Phan\Phan',
    );

    public function __invoke()
    {
        $this->tool->errorsType = $this->config->value('phpcs.ignoreWarnings') === true;
        $args = [$this->multiArg($this->options->getAnalyzedDirs(), 'directory')];
        $args['allow-polyfill-parser'] = null;
        $args['exclude-directory-list'] = implode(',', $this->options->ignore->psalm()['directory']);
        if ($this->options->isSavedToFiles) {
            $args['output'] = $this->options->rawFile('phan.xml');
            $args['output-mode'] = 'checkstyle';
        }

        return $args;
    }

    protected function multiArg($value, $optionPrefix)
    {
        if (count($value) === 0) {
            return '';
        }

        $fullOptionPrefix = '--'.$optionPrefix.static::getToolSettings()['optionSeparator'];

        return $fullOptionPrefix . implode(' ' . $fullOptionPrefix, $value);
    }

    /**
     * Get composer vendor/package name
     *
     * @return string
     */
    public static function getComposer()
    {
        return static::getToolSettings()['composer'];
    }

    /**
     * Get the tool display name
     *
     * @return string
     */
    public static function getToolName()
    {
        return 'Phan';
    }

    /**
     * Get tool report xsl filename (or {@code null} for cli)
     *
     * @return string|null
     */
    public static function getReportName()
    {
        return __DIR__.'/../../../app/report/phan.xsl';
    }

    /**
     * Get tool cli name
     *
     * @return string
     */
    public static function getCliName()
    {
        return 'phan';
    }

    /**
     * Get the FQCN of the tool
     * (use to check if the tool exist trough the autoloader)
     *
     * @return string
     */
    public static function getInternalClass()
    {
        return 'Phan\Phan';
    }

    /**
     * Return the global tool settings.
     *
     * @return array
     */
    public static function getToolSettings()
    {
        return self::$SETTINGS;
    }
}
