<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\shared;

final class Position
{
    /** @var int */
    private $x;
    /** @var int */
    private $y;
    /** @var int */
    private $z;

    public function __construct(int $x, int $y, int $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getZ(): int
    {
        return $this->z;
    }

    public function toKey(): string
    {
        return $this->x . ':' . $this->y . ':' . $this->z;
    }

    public function equals(Position $position): bool
    {
        return $position->getX() === $this->x && $position->getY() === $this->y && $position->getZ() === $this->z;
    }
}
