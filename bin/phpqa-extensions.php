<?php

$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php', // As main project
    __DIR__ . '/../../../autoload.php', // As dependency
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

$options = getopt('', ['add:', 'tools', 'enable:']);

$style = new \Symfony\Component\Console\Style\SymfonyStyle(
    new \Symfony\Component\Console\Input\ArgvInput(),
    new \Symfony\Component\Console\Output\ConsoleOutput()
);

$toolsFinder = new \MacFJA\PHPQAExtensions\ToolsFinder();

$availables = $toolsFinder->getAvailableTools();

if (array_key_exists('tools', $options)) {
    $style->title('List of available tools');


    $rows = [];
    foreach ($availables as $item) {
        $rows[] = [
            'name' => $item['name'],
            'toolName' => $item['cli'],
            'composer' => $item['composer'],
            'installed' => $toolsFinder->isToolInstalled($item) ? '<info>Yes</info>' : '<error> No </error>'
        ];
    }
    $style->table(['Name', 'CLI', 'Composer', 'Installed'], $rows);
    
    exit(0);
}

if (array_key_exists('add', $options)) {
    $toAdd = (array)$options['add'];
    $installer = new \MacFJA\PHPQAExtensions\ToolInstaller();

    foreach ($toAdd as $name) {
        $tool = $toolsFinder->findToolFrom($name);
        
        if ($tool == null) {
            $style->warning(sprintf('"%s" is not an available tool.', $name));
            continue;
        }

        if ($toolsFinder->isToolInstalled($tool)) {
            $style->warning(sprintf('"%s" is already installed.', $name));
            continue;
        }

        $installer->install($tool['classname']);
    }

    exit(0);
}

if (array_key_exists('enable', $options)) {
    $installer = new \MacFJA\PHPQAExtensions\ToolInstaller();

    list($name, $class, $report) = array_pad(explode(':', $options['enable'], 3), 3, null);

    if (empty($report)) {
        $report = null;
    }

    $installer->updatePhpQaConfig($name, $class, $report);
    $style->success('Tool enabled');

    exit(0);
}

$style->section('Usage');
$style->text([
    'phpqa-extensions.php --tools',
    'phpqa-extensions.php --add TOOL_NAME [--add TOOL_NAME ...]',
    'phpqa-extensions.php --enable TOOL_DATA'
]);

$style->section('Arguments');
$style->listing([
    '<info>TOOL_NAME</info>        The name of the tool. It can be the composer package, the cli name, or the display name (cf. "<info>--tools</info>" output)',
    '<info>TOOL_DATA</info>        The command information to enable. The syntax is "<question>$CLI_NAME$</question>:<question>$WRAPPER_CLASS$</question>" or "<question>$CLI_NAME$</question>:<question>$WRAPPER_CLASS$</question>:<question>$REPORT_PATH$</question>"',
    '<info>$CLI_NAME$</info>       The name of the CLI command',
    '<info>$WRAPPER_CLASS$</info>  The class that will be used by PHPQA to call the tool',
    '<info>$REPORT_PATH$</info>    The relative path of the XLST file to do the HTML transformation (optional)',
]);

$style->section('Examples');
$style->listing([
    'phpqa-extensions.php --tools',
    'phpqa-extensions.php --add phpmnd',
    'phpqa-extensions.php --add phpmnd --add phpa',
    'phpqa-extensions.php --enable phpmnd:\MacFJA\PHPQAExtensions\Tools\Analyzer\PhpMagicNumber:app/report/phpmagicnumber.xsl',
]);
