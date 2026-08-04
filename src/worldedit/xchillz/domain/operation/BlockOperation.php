<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\operation;

use worldedit\xchillz\domain\shared\Uuid;

final class BlockOperation
{
    /** @var Uuid */
    private $uuid;
    /** @var \Generator */
    private $pendingSource;
    /** @var int */
    private $totalCount;
    /** @var BlockChange[] */
    private $appliedChanges;
    /** @var int */
    private $status;
    /** @var BatchConfig */
    private $batchConfig;
    /** @var string */
    private $worldName;

    public function __construct(BatchConfig $batchConfig, string $worldName, \Generator $pendingSource, int $totalCount)
    {
        $this->uuid = Uuid::generate();
        $this->pendingSource = $pendingSource;
        $this->totalCount = $totalCount;
        $this->appliedChanges = [];
        $this->status = OperationStatus::PENDING;
        $this->batchConfig = $batchConfig;
        $this->worldName = $worldName;
    }

    /**
     * @return BlockChange[]
     */
    public function nextBatch(): array
    {
        $batchSize = $this->batchConfig->getSize();
        $batch = [];

        for ($i = 0; $i < $batchSize; $i++) {
            if (!$this->pendingSource->valid()) {
                break;
            }
            $batch[] = $this->pendingSource->current();
            $this->pendingSource->next();
        }

        return $batch;
    }

    public function recordApplied(BlockChange $change)
    {
        $this->appliedChanges[] = $change;
    }

    public function isComplete(): bool
    {
        return !$this->pendingSource->valid();
    }

    public function getProgress(): float
    {
        if ($this->totalCount === 0) {
            return 1.0;
        }
        return count($this->appliedChanges) / $this->totalCount;
    }

    public function getId(): Uuid
    {
        return $this->uuid;
    }

    public function getWorldName(): string
    {
        return $this->worldName;
    }

    public function getBatchConfig(): BatchConfig
    {
        return $this->batchConfig;
    }

    public function getAppliedChanges(): array
    {
        return $this->appliedChanges;
    }

    public function markStatus(int $status)
    {
        $this->status = $status;
    }
}
