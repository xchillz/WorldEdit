<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\operation;

use worldedit\xchillz\domain\shared\Uuid;

final class BlockOperation
{
    /** @var Uuid */
    private $uuid;
    /** @var \SplQueue */
    private $pendingChanges;
    /** @var BlockChange[] */
    private $appliedChanges;
    /** @var int */
    private $status;
    /** @var BatchConfig */
    private $batchConfig;
    /** @var string */
    private $worldName;

    public function __construct(BatchConfig $batchConfig, string $worldName)
    {
        $this->uuid = Uuid::generate();
        $this->pendingChanges = new \SplQueue();
        $this->appliedChanges = [];
        $this->status = OperationStatus::PENDING;
        $this->batchConfig = $batchConfig;
        $this->worldName = $worldName;
    }

    public function enqueue(BlockChange $blockChange)
    {
        $this->pendingChanges->enqueue($blockChange);
    }

    /**
     * @param iterable $blockChanges
     */
    public function enqueueAll($blockChanges)
    {
        foreach ($blockChanges as $blockChange) {
            $this->enqueue($blockChange);
        }
    }

    public function nextBatch(): array
    {
        $batchSize = $this->batchConfig->getSize();
        $appliedChanges = [];

        for ($i = 0; $i < $batchSize; $i++) {
            if ($this->pendingChanges->isEmpty()) {
                break;
            }
            $blockChange = $this->pendingChanges->dequeue();
            $appliedChanges[] = $blockChange;
            $this->appliedChanges[] = $blockChange;
        }

        return $appliedChanges;
    }

    public function isComplete(): bool
    {
        return $this->pendingChanges->isEmpty();
    }

    public function getProgress(): float
    {
        $total = count($this->appliedChanges) + $this->pendingChanges->count();

        if ($total === 0) {
            return 0.0;
        }

        return count($this->appliedChanges) / $total;
    }

    public function getAppliedChanges(): array
    {
        return $this->appliedChanges;
    }

    public function markStatus(int $status)
    {
        $this->status = $status;
    }

    public function getId(): Uuid
    {
        return $this->uuid;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getWorldName(): string
    {
        return $this->worldName;
    }

    public function getBatchConfig(): BatchConfig
    {
        return $this->batchConfig;
    }
}
