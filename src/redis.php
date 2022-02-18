<?php

/**
 * Redis inspection page
 *
 * PHP Version ~8.1.0
 *
 * @package  CacheInspection
 * @author   Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @license  MIT License
 * @link     https://github.com/infiniteloop-inc/php-cache-inspection/
 */

declare(strict_types=1);

namespace CacheInspection;

require_once __DIR__ . '/../vendor/autoload.php';

$redis = new \Redis();
$redis->connect('redis', 6379);

$add = 0;
$ttl = 10;

if (array_key_exists('add', $_GET) && $_GET['add']) {
    $add = max(1, (int)$_GET['add']);
    if (array_key_exists('ttl', $_GET) && $_GET['ttl']) {
        $ttl = (int)$_GET['ttl'];
    }
    $result = $redis->set((string)microtime(), str_repeat('0', $add), ['ex' => $ttl]);
    if (!$result) {
        echo 'Failed to add';
    } else {
        echo 'Added ' . number_format($add) . ' bytes. ttl=' . $ttl;
    }
}

$infoKeyNames = [
    'used_memory',
    'used_memory_human',
    'used_memory_rss',
    'used_memory_rss_human',
    'used_memory_peak',
    'used_memory_peak_human',
    'used_memory_peak_perc',
];

$info = $redis->info('MEMORY');
$redis->close();

?><!DOCTYPE html>
<head>
    <title>PHP Cache Inspection Project - Redis</title>
    <meta charset="utf-8"/>
</head>
<body>
    <h1>PHP Cache Inspection Project - Redis</h1>
    <p>
        See detail at <a href="https://github.com/phpredis/phpredis/#readme" target="_blank">https://github.com/phpredis/phpredis/#readme</a>
    </p>
    <h2>REDIS INFO MEMORY</h2>
    <p>
        <ul>
            <?php foreach ($infoKeyNames as $key) : ?>
                <li><?= $key ?> = <?= $info[$key] ?></li>
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
    <?php include 'Footer.php'; ?>
</body>
</html>
