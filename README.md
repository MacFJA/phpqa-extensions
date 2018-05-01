# PHPQA Extensions

Add more tools to [PHPQA](https://github.com/EdgedesignCZ/phpqa)

## List of tools

 - [PHP Magic Number Detector](https://github.com/povils/phpmnd): PHP Magic Number Detector.
 - [PhpAssumptions](https://github.com/rskuipers/php-assumptions): Tool to detect assumptions.
 - [PhpCodeAnalyzer](https://github.com/wapmorgan/PhpCodeAnalyzer): PhpCodeAnalyzer finds usage of non-built-in extensions in your php code.

## Usage

### Get the list of available tools

```
$ php vendor/bin/phpqa-extensions.php --tools

List of available tools
=======================

 --------------------------- -------- ----------------------------- ----------- 
  Name                        CLI      Composer                      Installed  
 --------------------------- -------- ----------------------------- ----------- 
  PhpAssumptions              phpa     rskuipers/php-assumptions     No        
  PhpCodeAnalyzer             phpca    wapmorgan/php-code-analyzer   No        
  PHP Magic Number Detector   phpmnd   povils/phpmnd                 No        
 --------------------------- -------- ----------------------------- ----------- 
```

### Install a tool on a project

```
$ php vendor/bin/phpqa-extensions.php --add phpmnd
```
or
```
$ php vendor/bin/phpqa-extensions.php --add "PHP Magic Number Detector"
```
or
```
$ php vendor/bin/phpqa-extensions.php --add povils/phpmnd
```

Several tools can be add in one times:
```
$ php vendor/bin/phpqa-extensions.php --add phpmnd --add phpa
```

### Enable an external tool

```
$ php vendor/bin/phpqa-extensions.php --enable phpmnd:\MacFJA\PHPQAExtensions\Tools\Analyzer\PhpMagicNumber:app/report/phpmagicnumber.xsl
```

The syntax of the parameter `--enabled` is: `$CLI_NAME$:$WRAPPER_CLASS$` or `$CLI_NAME$:$WRAPPER_CLASS$:$REPORT_PATH$`.
With:

 - `$CLI_NAME$`: The name of the CLI command
 - `$WRAPPER_CLASS$`: The class that will be used by PHPQA to call the tool
 - `$REPORT_PATH$`: The relative path of the XLST file to do the HTML transformation (optional)

## Options

| Option name | Default | Tool | Description |
|-------------|---------|------|-------------|
| phpmnd.ignore-numbers | _null_ | PHP Magic Number Detector | List (comma separate) of number to ignore (typically 0,1,2) |
| phpmnd.ignore-funcs | _null_ | PHP Magic Number Detector | List (comma separate) of function to ignore |
| phpmnd.ignore-strings | _null_ | PHP Magic Number Detector | List (comma separate) of strings value to ignore |
| phpmnd.strings | `false` | PHP Magic Number Detector | Activate the strings literal analysis |

## How to contribute

If you found a nice tool that you want to added, [open a issue on GitHub](https://github.com/MacFJA/phpqa-extensions/issues/new?template=new-tool.md).

You can also create Pull Request of a new tool.