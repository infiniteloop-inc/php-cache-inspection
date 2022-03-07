<?php

/**
 * APCu inspection page
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
    'apc.enabled',
    'apc.shm_segments',
    'apc.shm_size',
    'apc.entries_hint',
    'apc.ttl',
    'apc.gc_ttl',
    'apc.mmap_file_mask',
    'apc.slam_defense',
    'apc.enable_cli',
    'apc.use_request_time',
    'apc.serializer',
    'apc.coredump_unmap',
    'apc.preload_path',
];

$cacheInfoNames = [
    'num_slots',
    'ttl',
    'num_hits',
    'num_misses',
    'num_inserts',
    'num_entries',
    'expunges',
];

$add = 0;
$ttl = 10;

if (array_key_exists('add', $_GET) && $_GET['add']) {
    $add = max(1, (int)$_GET['add']);
    if (array_key_exists('ttl', $_GET) && $_GET['ttl']) {
        $ttl = (int)$_GET['ttl'];
    }
    $result = apcu_add((string)microtime(), str_repeat('0', $add), $ttl);
    if (!$result) {
        echo 'Failed to add';
    } else {
        echo 'Added ' . number_format($add) . ' bytes. ttl=' . $ttl;
    }
}

$cacheInfo = apcu_cache_info();

?><!DOCTYPE html>
<head>
    <title>PHP Cache Inspection Project - APCu</title>
    <meta charset="utf-8"/>
</head>
<body>
    <h1>PHP Cache Inspection Project - APCu</h1>
    <p>
        See detail at <a href="https://www.php.net/manual/ja/book.apcu.php" target="_blank" rel="noreferrer noopener">https://www.php.net/manual/ja/book.apcu.php</a>
    </p>
    <h2>ini directives</h2>
    <p>
        <?php (new IniInfo($directives))->render(); ?>
    </p>
    <h2>apcu_cache_info()</h2>
    <p><ul>
        <?php foreach ($cacheInfoNames as $name) : ?>
            <li><?= $name ?> = <?= number_format($cacheInfo[$name]) ?></li>
        <?php endforeach; ?>
        <li>mem_size = <?= sprintf('%.2f', $cacheInfo['mem_size'] / 1024 / 1024); ?> MB</li>
    </ul></p>
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
