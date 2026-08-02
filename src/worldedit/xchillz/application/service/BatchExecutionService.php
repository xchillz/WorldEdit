<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\service;

use worldedit\xchillz\application\port\TaskScheduler;
use worldedit\xchillz\domain\operation\BlockOperation;
use worldedit\xchillz\domain\operation\OperationStatus;

final class BatchExecutionService
{
    /** @var TaskScheduler */
    private $taskScheduler;

    public function __construct(TaskScheduler $taskScheduler)
    {
        $this->taskScheduler = $taskScheduler;
    }

    public function start(BlockOperation $blockOperation, string $playerName)
    {
        $this->taskScheduler->schedule($blockOperation, $playerName);

        $blockOperation->markStatus(OperationStatus::RUNNING);
    }
}
