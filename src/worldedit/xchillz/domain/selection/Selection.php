<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\selection;

use worldedit\xchillz\domain\shared\Position;

final class Selection
{
    /** @var Position */
    private $firstPosition;
    /** @var Position */
    private $secondPosition;
    /** @var string */
    private $worldName;

    public function setFirstPosition(Position $firstPosition)
    {
        $this->firstPosition = $firstPosition;
    }

    public function setSecondPosition(Position $secondPosition)
    {
        $this->secondPosition = $secondPosition;
    }

    public function setWorldName(string $worldName)
    {
        $this->worldName = $worldName;
    }

    public function getFirstPosition(): Position
    {
        return $this->firstPosition;
    }

    public function getSecondPosition(): Position
    {
        return $this->secondPosition;
    }

    public function getWorldName(): string
    {
        return $this->worldName;
    }

    public function isComplete(): bool
    {
        return isset($this->firstPosition) && isset($this->secondPosition) && isset($this->worldName);
    }
}
