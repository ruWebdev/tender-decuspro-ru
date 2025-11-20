<?php

namespace App\Services;

use App\Models\SystemLog;

class SystemLogService
{
    public function error(string $code, string $message, array $context = []): void
    {
        $this->write('error', $code, $message, $context);
    }

    public function warning(string $code, string $message, array $context = []): void
    {
        $this->write('warning', $code, $message, $context);
    }

    public function info(string $code, string $message, array $context = []): void
    {
        $this->write('info', $code, $message, $context);
    }

    public function business(string $code, string $message, array $context = []): void
    {
        $this->write('business', $code, $message, $context);
    }

    protected function write(string $level, string $code, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'code' => $code,
            'message' => $message,
            'context' => $context ?: null,
        ]);
    }
}
