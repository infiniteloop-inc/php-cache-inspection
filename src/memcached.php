<?php

/**
 * Memcached inspection page
 *
 * PHP Version ~8.1.0
 *
 * @package   CacheInspection
 * @author    Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @license   MIT License
 * @copyright infiniteloop Co.,Ltd
 * @link      https://github.com/infiniteloop-inc/php-cache-inspection/
 */

declare(strict_types=1);

namespace CacheInspection;

require_once __DIR__ . '/../vendor/autoload.php';

$directives = [
    'memcached.sess_locking',
    'memcached.sess_consistent_hash',
    'memcached.sess_binary',
    'memcached.sess_lock_wait',
    'memcached.sess_prefix',
    'memcached.sess_number_of_replicas',
    'memcached.sess_randomize_replica_read',
    'memcached.sess_remove_failed',
    'memcached.compression_type',
    'memcached.compression_factor',
    'memcached.compression_threshold',
    'memcached.serializer',
    'memcached.use_sasl',
    'memcached.default_binary_protocol',
    'memcached.default_connect_timeout',
    'memcached.default_consistent_hash',
    'memcached.sess_binary_protocol',
    'memcached.sess_connect_timeout',
    'memcached.sess_consistent_hash_type',
    'memcached.sess_lock_expire',
    'memcached.sess_lock_retries',
    'memcached.sess_lock_wait_max',
    'memcached.sess_lock_wait_min',
    'memcached.sess_persistent',
    'memcached.sess_remove_failed_servers',
    'memcached.sess_server_failure_limit',
    'memcached.sess_sasl_password',
    'memcached.sess_sasl_username',
    'memcached.store_retry_count',
];

$memcached = new \Memcached();
$memcached->addServer('memcached', 11211);

if (array_key_exists('reset', $_GET)) {
    $memcached->flush();
    echo 'Flushed.';
}

$add = 1;
$ttl = 10;

if (array_key_exists('add', $_GET) && $_GET['add']) {
    $add = max(1, (int)$_GET['add']);
    if (array_key_exists('ttl', $_GET)) {
        $ttl = max(0, (int)$_GET['ttl']);
    }
    $result = $memcached->set(str_replace(' ', '_', (string)microtime()), str_repeat('0', $add), $ttl);
    if (!$result) {
        echo 'Failed to add code=' . $memcached->getResultMessage();
    } else {
        echo 'Added ' . number_format($add) . ' bytes. ttl=' . $ttl;
    }
}

$allStats = $memcached->getStats()['memcached:11211'];
$statKeys = [
    'curr_connections',
    'total_connections',
    'bytes',
    'curr_items',
    'total_items',
    'evictions',
    'reclaimed',
];
$stats = [];
foreach ($statKeys as $key) {
    $stats[$key] = $allStats[$key];
}

$memcached->quit();

?><!DOCTYPE html>
<head>
    <title>PHP Cache Inspection Project - Memcached</title>
    <meta charset="utf-8"/>
</head>
<body>
    <h1>PHP Cache Inspection Project - Memcached</h1>
    <p>
        See detail at <a href="https://www.php.net/manual/ja/book.memcached" target="_blank">https://www.php.net/manual/ja/book.memcached</a>
    </p>
    <h2>ini directives</h2>
    <p>
        <?php (new IniInfo($directives))->render(); ?>
    </p>
    <h2>Memcached::getStats()</h2>
    <p>
        <ul>
            <?php foreach ($stats as $key => $value) : ?>
                <li><?= $key . ' = ' . number_format((float)$value); ?></li>
            <?php endforeach; ?>
        </ul>
    </p>
    <p>
        <form action="" method="GET">
            <label>add bytes: <input type="number" name="add" min="1" value="<?= $add ?>"/></label><br />
            <label>TTL: <input type="number" name="ttl" min="0" value="<?= $ttl ?>"/></label><br />
            <button type="submit">Send</button>
        </form>
    </p>
    <p>
        <form action="" method="GET">
            <input type="hidden" name="reset" value="1"/>
            <button type="submit">Reset</button>
        </form>
    </p>
    <?php include 'Footer.php'; ?>
</body>
</html>
