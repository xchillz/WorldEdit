<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\usecase;

use worldedit\xchillz\application\service\BatchExecutionService;
use worldedit\xchillz\domain\operation\BlockChange;
use worldedit\xchillz\domain\operation\BlockOperation;
use worldedit\xchillz\domain\session\SessionRepository;

final class UndoUseCase
{
    /** @var SessionRepository */
    private $sessionRepository;
    /** @var BatchExecutionService */
    private $batchExecutionService;

    public function __construct(SessionRepository $sessionRepository, BatchExecutionService $batchExecutionService)
    {
        $this->sessionRepository = $sessionRepository;
        $this->batchExecutionService = $batchExecutionService;
    }

    public function execute(string $playerName): BlockOperation
    {
        $session = $this->sessionRepository->get($playerName);

        $lastOperation = $session->getLastOperation();
        if ($lastOperation === null) {
            throw new \Exception("Nothing to undo");
        }

        $inverseChanges = [];
        foreach ($lastOperation->getAppliedChanges() as $change) {
            $previous = $change->getPreviousState();
            if ($previous === null) {
                continue;
            }

            $inverseChanges[] = new BlockChange($change->getPosition(), $previous->getBlockId(), $previous->getBlockMeta());
        }

        if (count($inverseChanges) === 0) {
            throw new \Exception("Nothing to undo");
        }

        $undoOperation = new BlockOperation($lastOperation->getBatchConfig(), $lastOperation->getWorldName());
        $undoOperation->enqueueAll($inverseChanges);

        $this->batchExecutionService->start($undoOperation, $playerName);
        $session->moveHistoryPointerBack();

        return $undoOperation;
    }
}
