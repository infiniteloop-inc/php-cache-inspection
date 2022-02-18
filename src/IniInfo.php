<?php

/**
 * Show ini info
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

/**
 * Shows ini
 */
class IniInfo
{
    /**
     * @param string[] $names ini directive name list
     */
    public function __construct(public array $names)
    {
    }

    public function render(): void
    {
        echo '<ul>';
        foreach ($this->names as $directive) {
            $value = ini_get($directive);
            echo '<li>' . $directive . ' = ' . $value . '</li>';
        }
        echo '</ul>';
    }
}
