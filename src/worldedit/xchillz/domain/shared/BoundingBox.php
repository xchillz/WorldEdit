<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\shared;

final class BoundingBox
{
    /** @var Position */
    private $min;
    /** @var Position */
    private $max;

    public function __construct(Position $positionA, Position $positionB)
    {
        $this->min = new Position(
            min($positionA->getX(), $positionB->getX()),
            min($positionA->getY(), $positionB->getY()),
            min($positionA->getZ(), $positionB->getZ())
        );
        $this->max = new Position(
            max($positionA->getX(), $positionB->getX()),
            max($positionA->getY(), $positionB->getY()),
            max($positionA->getZ(), $positionB->getZ())
        );
    }

    public function getMin(): Position
    {
        return $this->min;
    }

    public function getMax(): Position
    {
        return $this->max;
    }

    public function getBlockCount(): int
    {
        return ($this->max->getX() - $this->min->getX() + 1)
            * ($this->max->getY() - $this->min->getY() + 1)
            * ($this->max->getZ() - $this->min->getZ() + 1);
    }

    /**
     * @return \Generator|Position[]
     */
    public function each(): \Generator
    {
        for ($x = $this->min->getX(); $x <= $this->max->getX(); $x++) {
            for ($y = $this->min->getY(); $y <= $this->max->getY(); $y++) {
                for ($z = $this->min->getZ(); $z <= $this->max->getZ(); $z++) {
                    yield new Position($x, $y, $z);
                }
            }
        }
    }

    public function isInside(Position $position): bool
    {
        return $position->getX() >= $this->min->getX() && $position->getX() <= $this->max->getX()
            && $position->getY() >= $this->min->getY() && $position->getY() <= $this->max->getY()
            && $position->getZ() >= $this->min->getZ() && $position->getZ() <= $this->max->getZ();
    }
}
