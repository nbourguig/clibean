<?php
use Symfony\Component\Console\Application;
use Clibean\Components\Config;
use Clibean\Tasks\Command;

require_once 'vendor/autoload.php';

// Application directory
define('APP_DIR', __DIR__);

// Config stuff
Config::setEnvironnement('local');
Config::setConfigDirectory(__DIR__ . '/config');

// Console application
$application = new Application(Config::getClibeanArt(), Config::get('app.version'));
$application->addCommands(array(
    new Command\BeanstalkUserMe(),
    new Command\BeanstalkRepositoryInfos(),
    new Command\BeanstalkRepositoryReleases(),
    new Command\BeanstalkRepositoryList(),
));

// run console
$application->run();
