<?php

define('SHELLROOT_', dirname(dirname(__FILE__)) . '/');
require(SHELLROOT_ . 'includes/console/shelldispatcher.php');

ShellDispatcher::run($argv);