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

$options = getopt('', ['add:', 'tools']);

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
