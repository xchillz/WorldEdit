<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\operation;

use worldedit\xchillz\domain\shared\Position;

final class BlockChange
{
    /** @var Position */
    private $position;
    /** @var int */
    private $blockId;
    /** @var int */
    private $blockMeta;

    public function __construct(Position $position, int $blockId, int $blockMeta)
    {
        $this->position = $position;
        $this->blockId = $blockId;
        $this->blockMeta = $blockMeta;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getBlockId(): int
    {
        return $this->blockId;
    }

    public function getBlockMeta(): int
    {
        return $this->blockMeta;
    }

    public function equals(BlockChange $blockChange): bool
    {
        return $this === $blockChange;
    }
}
