<?php

$target = dirname(__DIR__).'/vendor/laravel/framework/config/database.php';

if (! file_exists($target)) {
    fwrite(STDOUT, "[fix-laravel-pdo] Skipped: vendor config not found.\n");
    return;
}

$contents = file_get_contents($target);

if ($contents === false) {
    fwrite(STDERR, "[fix-laravel-pdo] Failed: unable to read vendor config.\n");
    exit(1);
}

if (str_contains($contents, "defined('Pdo\\\\Mysql::ATTR_SSL_CA')")) {
    fwrite(STDOUT, "[fix-laravel-pdo] Already compatible with PHP 8.5.\n");
    return;
}

$originalHeader = <<<'PHP'
use Illuminate\Support\Str;

return [
PHP;

$patchedHeader = <<<'PHP'
use Illuminate\Support\Str;

$mysqlSslCa = defined('Pdo\\Mysql::ATTR_SSL_CA')
    ? \Pdo\Mysql::ATTR_SSL_CA
    : PDO::MYSQL_ATTR_SSL_CA;

return [
PHP;

$contents = str_replace($originalHeader, $patchedHeader, $contents, $headerCount);
$contents = str_replace(
    "PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),",
    "\$mysqlSslCa => env('MYSQL_ATTR_SSL_CA'),",
    $contents,
    $optionCount
);

if ($headerCount !== 1 || $optionCount !== 2) {
    fwrite(STDERR, "[fix-laravel-pdo] Failed: unexpected vendor config format.\n");
    exit(1);
}

if (file_put_contents($target, $contents) === false) {
    fwrite(STDERR, "[fix-laravel-pdo] Failed: unable to write vendor config.\n");
    exit(1);
}

fwrite(STDOUT, "[fix-laravel-pdo] Patched vendor config for PHP 8.5 compatibility.\n");
