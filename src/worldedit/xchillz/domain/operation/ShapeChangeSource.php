<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\operation;

use worldedit\xchillz\domain\selection\Shape;

final class ShapeChangeSource
{
    public static function fromShape(Shape $shape, int $blockId, int $blockMeta): \Generator
    {
        foreach ($shape->getBoundingBox()->each() as $pos) {
            if (!$shape->contains($pos)) {
                continue;
            }
            yield new BlockChange($pos, $blockId, $blockMeta);
        }
    }
}
