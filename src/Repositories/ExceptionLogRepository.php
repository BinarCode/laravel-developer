<?php

namespace Binarcode\LaravelDeveloper\Repositories;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use DateTimeInterface;

class ExceptionLogRepository
{
    private ExceptionLog $exceptionLog;

    public function __construct(ExceptionLog $exceptionLog)
    {
        $this->exceptionLog = $exceptionLog;
    }

    public function prune(DateTimeInterface $before)
    {
        $query = $this->exceptionLog::query()->where('created_at', '<', $before);

        $totalDeleted = 0;

        do {
            $deleted = $query->take(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }
}
