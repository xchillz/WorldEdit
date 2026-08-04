<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\usecase;

use worldedit\xchillz\application\port\WorldReader;
use worldedit\xchillz\application\service\BatchExecutionService;
use worldedit\xchillz\domain\operation\BatchConfig;
use worldedit\xchillz\domain\operation\BlockOperation;
use worldedit\xchillz\domain\operation\ShapeChangeSource;
use worldedit\xchillz\domain\selection\ShapeFactory;
use worldedit\xchillz\domain\selection\ShapeType;
use worldedit\xchillz\domain\session\SessionRepository;

final class SetBlocksUseCase
{
    const MAX_BLOCK_COUNT = 2000000;

    /** @var SessionRepository */
    private $sessionRepository;
    /** @var WorldReader */
    private $worldReader;
    /** @var BatchExecutionService */
    private $batchExecutionService;

    public function __construct(SessionRepository $sessionRepository, WorldReader $worldReader, BatchExecutionService $batchExecutionService)
    {
        $this->sessionRepository = $sessionRepository;
        $this->worldReader = $worldReader;
        $this->batchExecutionService = $batchExecutionService;
    }

    public function execute(string $playerName, int $blockId, int $blockMeta, int $batchSize): BlockOperation
    {
        $session = $this->sessionRepository->get($playerName);

        if (!$session->getSelection()->isComplete()) {
            throw new \Exception("Incomplete selection");
        }

        $worldName = $session->getSelection()->getWorldName();

        $shape = ShapeFactory::create(
            ShapeType::CUBOID,
            $session->getSelection()->getFirstPosition(),
            $session->getSelection()->getSecondPosition()
        );

        $source = ShapeChangeSource::fromShape($shape, $blockId, $blockMeta);
        $operation = new BlockOperation(new BatchConfig($batchSize), $worldName, $source, $shape->getBlockCount());

        $this->batchExecutionService->start($operation, $playerName);
        $session->pushHistory($operation);

        return $operation;
    }
}
