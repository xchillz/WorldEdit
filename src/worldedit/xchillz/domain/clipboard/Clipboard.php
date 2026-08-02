<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\clipboard;

use worldedit\xchillz\domain\operation\BlockChange;
use worldedit\xchillz\domain\shared\Position;

final class Clipboard
{
    /** @var array<string, BlockChange>  */
    private $blocks;
    /** @var Position */
    private $origin;

    public function __construct(Position $origin)
    {
        $this->origin = $origin;
        $this->blocks = [];
    }

    public function addBlock(BlockChange $blockChange)
    {
        $key = $blockChange->getPosition()->toKey();
        $this->blocks[$key] = $blockChange;
    }

    public function getOrigin(): Position
    {
        return $this->origin;
    }

    public function getBlocksRelativeTo(Position $newOrigin): array
    {
        $translated = [];

        foreach ($this->blocks as $change) {
            $pos = $change->getPosition();
            $newPos = new Position(
                $newOrigin->getX() + $pos->getX(),
                $newOrigin->getY() + $pos->getY(),
                $newOrigin->getZ() + $pos->getZ()
            );

            $translated[] = new BlockChange($newPos, $change->getBlockId(), $change->getBlockMeta());
        }

        return $translated;
    }
}
