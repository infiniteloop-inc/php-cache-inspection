<?php

/**
 * Opcache inspection page
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

ini_set('memory_limit', '512M');

require_once __DIR__ . '/../vendor/autoload.php';

$directives = [
    'opcache.enable',
    'opcache.enable_cli',
    'opcache.memory_consumption',
    'opcache.interned_strings_buffer',
    'opcache.max_accelerated_files',
    'opcache.max_wasted_percentage',
    'opcache.use_cwd',
    'opcache.validate_timestamps',
    'opcache.revalidate_freq',
    'opcache.revalidate_path',
    'opcache.save_comments',
    'opcache.enable_file_override',
    'opcache.dups_fix',
    'opcache.blacklist_filename',
    'opcache.max_file_size',
    'opcache.consistency_checks',
    'opcache.force_restart_timeout',
    'opcache.error_log',
    'opcache.log_verbosity_level',
    'opcache.preferred_memory_model',
    'opcache.protect_memory',
    'opcache.mmap_base',
    'opcache.restrict_api',
    'opcache.file_update_protection',
    'opcache.huge_code_pages',
    'opcache.lockfile_path',
    'opcache.opt_debug_level',
    'opcache.file_cache',
    'opcache.file_cache_only',
    'opcache.file_cache_consistency_checks',
    'opcache.validate_permission',
    'opcache.validate_root',
    'opcache.preload',
    'opcache.preload_user',
];

$add = 0;

if (array_key_exists('reset', $_GET) && $_GET['reset']) {
    opcache_reset();
    header('Location: /opcache.php');
} elseif (array_key_exists('add', $_GET) && $_GET['add']) {
    $add = (int)floor(max(1, (int)$_GET['add'] / 3));

    $tmpfname = tempnam(sys_get_temp_dir(), 'OPCACHE_');
    $handle = fopen($tmpfname, 'w');
    fwrite($handle, '<?php return [' . str_repeat('[],', $add) . '];');
    $add *= 3;
    fclose($handle);
    touch($tmpfname, time() - 5);
    $result = opcache_compile_file($tmpfname);
    if (!$result) {
        echo 'Failed to compile';
    } elseif (!opcache_is_script_cached($tmpfname)) {
        echo 'Failed to cache';
    } else {
        echo 'Compiled ' . $tmpfname . ' with ' . number_format($add) . ' bytes.';
        require_once $tmpfname;
    }
}

$status = opcache_get_status(false);

?><!DOCTYPE html>
<head>
    <title>PHP Cache Inspection Project - Opcache</title>
    <meta charset="utf-8"/>
</head>
<body>
    <h1>PHP Cache Inspection Project - Opcache</h1>
    <p>
        See detail at <a href="https://www.php.net/manual/ja/book.opcache.php" target="_blank">https://www.php.net/manual/ja/book.opcache.php</a>
    </p>
    <h2>ini directives</h2>
    <p>
        <?php (new IniInfo($directives))->render(); ?>
    </p>
    <h2>opcache_get_status()</h2>
    <p>
        <ul>
            <li>opcache_enabled = <?= (int)$status['opcache_enabled'] ?></li>
            <li>cache_full = <?= (int)$status['cache_full'] ?></li>
            <li>restart_pending = <?= (int)$status['restart_pending'] ?></li>
            <li>restart_in_progress = <?= (int)$status['restart_in_progress'] ?></li>
            <li>used_memory = <?= sprintf('%.2f', $status['memory_usage']['used_memory'] / 1024 / 1024) ?> MB</li>
            <li>free_memory = <?= sprintf('%.2f', $status['memory_usage']['free_memory'] / 1024 / 1024) ?> MB</li>
            <li>used_memory_percentage = <?= sprintf('%.3f', $status['memory_usage']['used_memory'] / ($status['memory_usage']['used_memory'] + $status['memory_usage']['free_memory']) * 100) ?> %</li>
            <li>wasted_memory = <?= sprintf('%.2f', $status['memory_usage']['wasted_memory'] / 1024 / 1024) ?> MB</li>
            <li>current_wasted_percentage = <?= sprintf('%.3f', $status['memory_usage']['current_wasted_percentage']) ?> %</li>
            <li>num_cached_scripts = <?= $status['opcache_statistics']['num_cached_scripts'] ?></li>
            <li>oom_restarts = <?= $status['opcache_statistics']['oom_restarts'] ?></li>
            <li>hash_restarts = <?= $status['opcache_statistics']['hash_restarts'] ?></li>
        </ul>
    </p>
    <p>
        <form action="" method="GET">
            <label>add bytes: <input type="number" name="add" min="1" value="<?= $add ?>"/></label><br />
            <button type="submit">Send</button>
        </form>
    </p>
    <p>
        <form action="" method="GET">
            <input type="hidden" name="reset" value="true"/>
            <button type="submit">Reset</button>
        </form>
    </p>
    <?php include 'Footer.php'; ?>
</body>
</html>
