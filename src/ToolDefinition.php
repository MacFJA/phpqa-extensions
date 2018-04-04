<?php
namespace MacFJA\PHPQAExtensions;

/**
 * Interface to define how get tool information
 *
 * @author  MacFJA
 * @license MIT
 */
interface ToolDefinition
{
    /**
     * Get composer vendor/package name
     * @return string
     */
    public static function getComposer();

    /**
     * Get the tool display name
     * @return string
     */
    public static function getToolName();

    /**
     * Get tool report xsl filename (or {@code null} for cli)
     * @return string|null
     */
    public static function getReportName();

    /**
     * Get tool cli name
     * @return string
     */
    public static function getCliName();

    /**
     * Get the FQCN of the tool
     * (use to check if the tool exist trough the autoloader)
     * @return string
     */
    public static function getInternalClass();
}
