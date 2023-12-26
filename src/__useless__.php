<?php

// 只是为了消除IDE警告信息，无实际用途

if (mt_rand(0, 1) === -1) {

    if (!class_exists('Hyperf\\Process\\AbstractProcess')) {
        class hyperf_process_abstract_process{
            protected int $restartInterval;
            protected bool $enableCoroutine;
            protected int $nums;
            protected string $name;
            protected int $pipeType;
            protected bool $redirectStdinStdout;
            public function __construct(protected $container) {} }
        class_alias('hyperf_process_abstract_process', 'Hyperf\\Process\\AbstractProcess');
        class hyperf_crontab_process_crontab_dispatcher_process {}
        class_alias('hyperf_crontab_process_crontab_dispatcher_process', 'Hyperf\\Crontab\\Process\\CrontabDispatcherProcess');
    }

    if (extension_loaded('Swoole')) {
        defined('SWOOLE_BASE') || define('SWOOLE_BASE', 1);
        defined('SWOOLE_PROCESS') || define('SWOOLE_PROCESS', 2);
        defined('SWOOLE_SOCK_TCP') || define('SWOOLE_SOCK_TCP', 1);
        defined('SWOOLE_SOCK_UDP') || define('SWOOLE_SOCK_UDP', 2);
        if (!function_exists('swoole_cpu_num')) {
            function swoole_cpu_num(): int { return 1; }
        }
    } elseif (extension_loaded('Swow')) {
        if (!class_exists('swow\\Socket')) {
            class swow_socket { const TYPE_TCP = 16777233; const TYPE_UDP = 134217746; }
            class_alias(swow_socket::class, 'swow\\Socket');
        }
    }
}
