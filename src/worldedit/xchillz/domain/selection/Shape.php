<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\selection;

use worldedit\xchillz\domain\shared\BoundingBox;
use worldedit\xchillz\domain\shared\Position;

interface Shape
{
    public function contains(Position $position): bool;
    public function getBoundingBox(): BoundingBox;
    public function getBlockCount(): int;
}
