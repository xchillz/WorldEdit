<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\selection;

use worldedit\xchillz\domain\shared\BoundingBox;
use worldedit\xchillz\domain\shared\Position;

final class CuboidShape implements Shape
{
    /** @var BoundingBox */
    private $boundingBox;

    public function __construct(Position $positionA, Position $positionB)
    {
        $this->boundingBox = new BoundingBox($positionA, $positionB);
    }

    public function contains(Position $position): bool
    {
        return $this->boundingBox->isInside($position);
    }

    public function getBoundingBox(): BoundingBox
    {
        return $this->boundingBox;
    }

    public function getBlockCount(): int
    {
        return $this->boundingBox->getBlockCount();
    }
}
