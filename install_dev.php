<?php
/*
 * This script is intended to be a level of indirection between the user who is installing LoreCommander
 * and the composer so that we can specify which composer.json configuration to use upon installation.
 *
 * The reason we have two composer.json files is because we want to make it easy for users, testers and
 * developers of Zermelo-DURC to be able to either:
 * 1- Use the most recent official releases from Packagist
 * OR
 * 2- Use the most recent code from GitHub. So we have two composer.json files:
 *
 * 1- composer.json - Requires the latest stable dist packages from Packagist
 * 2- composer-dev.json - Requires the latest source code from github, and includes repository references
 *
 *
 */
// add the current unix user to the www-data group
$username = posix_getpwuid(posix_geteuid())['name'];
$output = shell_exec("sudo usermod -a -G careset $username") . "\n";
echo "Adding user $username to careset group...\n";

// based on option, should we use dev file, or regular file
$longopts = [
    "install",
    "update",
    "dev" // Option with no associated value, just a flag that specifies composer-dev.json by passing "--dev"
];

$options = getopt("iud", $longopts);

// Based on the first argument, are we going to "install" or "update"
$task = null;
if ($options["i"] === true ||
    isset($options["install"])) {
    $task = "install";
} else if ($options["u"] === true ||
    isset($options["update"])) {
    $task = "update";
}

if ($task ===null) {
    die("No task specified, please use --install or --update\n");
}

$composer = "COMPOSER=composer.json";
if ($options["d"] === true ||
    isset($options["dev"]) ) {
    $composer = "COMPOSER=composer-dev.json";
}

echo "Using $composer\n";

// Now determine composer command to run, we run use full path to avoid PATH variable problems
$composer_locations = [
    '/usr/local/bin/composer',
    '/bin/composer',
    '/usr/bin/composer',
    '/usr/local/bin/composer.phar',
    '/bin/composer.phar',
    '/usr/bin/composer.phar'
];

//we assume its missing
$is_got_composer = false;
foreach ($composer_locations as $composer_location) {
    if (file_exists($composer_location)) {
        $is_got_composer = true;
        break;
    }
}

if (!$is_got_composer) {
    die("You need to install composer");
}

// We need to prepend the composer command with 'php'
$composer_command = "php $composer_location";

// run clear-cache
system("$composer_command clear-cache");

// Run our composer command
$command = "$composer $composer_command $task";
system($command);
//$last_line = system($command, $return_status);

//echo "You need to run: \n$command\n";



// correct the group permissions for all the files in the project so that they can interact correctly with github and be run by any user:
// change the group for everything to 'careset':
shell_exec("sudo chown $username:careset * -R");
// Grant groups r/w privileges:
shell_exec("sudo chmod g+rw * -R");
// Add the sticky bit permission, but only to directories:
shell_exec("sudo find ./ -type d | xargs chmod g+s");
// Change group on the storage directory to be writable by apache:
shell_exec("sudo chown www-data:careset storage -R");

