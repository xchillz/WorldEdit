<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\usecase;

use worldedit\xchillz\application\service\BatchExecutionService;
use worldedit\xchillz\domain\operation\BatchConfig;
use worldedit\xchillz\domain\operation\BlockOperation;
use worldedit\xchillz\domain\session\SessionRepository;
use worldedit\xchillz\domain\shared\Position;

final class PasteUseCase
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

    public function execute(string $playerName, Position $target, int $batchSize): BlockOperation
    {
        $session = $this->sessionRepository->get($playerName);

        $clipboard = $session->getClipboard();
        if ($clipboard === null) {
            throw new \Exception("Empty clipboard");
        }

        $changes = $clipboard->getBlocksRelativeTo($target);
        if (count($changes) === 0) {
            throw new \Exception("Nothing to paste");
        }

        $operation = new BlockOperation(new BatchConfig($batchSize), $session->getSelection()->getWorldName());
        $operation->enqueueAll($changes);

        $this->batchExecutionService->start($operation, $playerName);
        $session->pushHistory($operation);

        return $operation;
    }
}
