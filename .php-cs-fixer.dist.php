<?php

$excludes = [
    'bootstrap/cache',
    'node_modules',
    'storage',
    'public',
    'docs',
];

$finder = PhpCsFixer\Finder::create()
    ->exclude($excludes)
    ->in(__DIR__)
    ->notName('*.blade.php')
    ->notName('_ide_*.php')
    ->append([
        __DIR__.'/dev-tools/doc.php',
    ])
;

$rules = [
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
];

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder)
;

// special handling of fabbot.io service if it's using too old PHP CS Fixer version
if (false !== getenv('FABBOT_IO')) {
    try {
        PhpCsFixer\FixerFactory::create()
            ->registerBuiltInFixers()
            ->registerCustomFixers($config->getCustomFixers())
        ;
    } catch (PhpCsFixer\ConfigurationException\InvalidConfigurationException $e) {
        $config->setRules([]);
    } catch (UnexpectedValueException $e) {
        $config->setRules([]);
    } catch (InvalidArgumentException $e) {
        $config->setRules([]);
    }
}

return $config;
