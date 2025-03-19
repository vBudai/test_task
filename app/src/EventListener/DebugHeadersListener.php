<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class DebugHeadersListener
{
    private float $startTime;
    private int $startMemory;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $executionTime = (microtime(true) - $this->startTime) * 1000;
        $memoryUsage   = (memory_get_usage() - $this->startMemory) / 1024;

        $response->headers->set('X-Debug-Time', round($executionTime, 2) . ' ms');
        $response->headers->set('X-Debug-Memory', round($memoryUsage, 2) . ' KB');
    }
}