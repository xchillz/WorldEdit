<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\selection;

use worldedit\xchillz\domain\shared\Position;

final class ShapeFactory
{
    public static function create(int $type, Position $firstPosition, Position $secondPosition): Shape
    {
        switch ($type) {
            case ShapeType::CUBOID:
                return new CuboidShape($firstPosition, $secondPosition);
            default:
                throw new \InvalidArgumentException("Unknown shape type: {$type}");
        }
    }
}
