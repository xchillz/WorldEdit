<?php

declare(strict_types=1);

namespace worldedit\xchillz\infrastructure\scheduler;

use pocketmine\scheduler\Task;
use worldedit\xchillz\application\port\ProgressNotifier;
use worldedit\xchillz\application\port\WorldWriter;
use worldedit\xchillz\domain\operation\BlockOperation;
use worldedit\xchillz\domain\operation\OperationStatus;

final class BatchOperationTask extends Task
{
    /** @var BlockOperation */
    private $operation;
    /** @var string */
    private $playerName;
    /** @var WorldWriter */
    private $worldWriter;
    /** @var ProgressNotifier */
    private $notifier;

    public function __construct(BlockOperation $operation, string $playerName, WorldWriter $worldWriter, ProgressNotifier $notifier)
    {
        $this->operation = $operation;
        $this->playerName = $playerName;
        $this->worldWriter = $worldWriter;
        $this->notifier = $notifier;
    }

    /**
     * @param int $currentTick
     */
    public function onRun($currentTick)
    {
        $batch = $this->operation->nextBatch();

        foreach ($batch as $change) {
            $this->worldWriter->setBlock($this->operation->getWorldName(), $change);
        }

        $this->notifier->notifyProgress($this->playerName, $this->operation->getProgress());

        if ($this->operation->isComplete()) {
            $this->operation->markStatus(OperationStatus::DONE);
            $this->notifier->notifyComplete($this->playerName, count($this->operation->getAppliedChanges()));
            $this->getHandler()->cancel();
        }
    }
}
