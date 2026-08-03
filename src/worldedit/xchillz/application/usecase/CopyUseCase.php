<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\usecase;

use worldedit\xchillz\application\port\WorldReader;
use worldedit\xchillz\domain\clipboard\Clipboard;
use worldedit\xchillz\domain\operation\BlockChange;
use worldedit\xchillz\domain\selection\ShapeFactory;
use worldedit\xchillz\domain\selection\ShapeType;
use worldedit\xchillz\domain\session\SessionRepository;
use worldedit\xchillz\domain\shared\Position;

final class CopyUseCase
{
    /** @var SessionRepository */
    private $sessionRepository;
    /** @var WorldReader */
    private $worldReader;

    public function __construct(SessionRepository $sessionRepository, WorldReader $worldReader)
    {
        $this->sessionRepository = $sessionRepository;
        $this->worldReader = $worldReader;
    }

    public function execute(string $playerName)
    {
        $session = $this->sessionRepository->get($playerName);

        if (!$session->getSelection()->isComplete()) {
            throw new \Exception("Incomplete selection");
        }

        $shape = ShapeFactory::create(
            ShapeType::CUBOID,
            $session->getSelection()->getFirstPosition(),
            $session->getSelection()->getSecondPosition()
        );

        $origin = $shape->getBoundingBox()->getMin(); // TODO: Find a more accurate way to do this.
        $clipboard = new Clipboard($origin);

        foreach ($shape->getBoundingBox()->each() as $pos) {
            if (!$shape->contains($pos)) {
                continue;
            }

            $worldBlock = $this->worldReader->getBlockAt($session->getSelection()->getWorldName(), $pos);

            $relativePos = new Position(
                $pos->getX() - $origin->getX(),
                $pos->getY() - $origin->getY(),
                $pos->getZ() - $origin->getZ()
            );

            $clipboard->addBlock(new BlockChange($relativePos, $worldBlock->getBlockId(), $worldBlock->getBlockMeta()));
        }

        $session->setClipboard($clipboard);
    }
}
