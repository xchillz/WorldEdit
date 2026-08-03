<?php

declare(strict_types=1);

namespace worldedit\xchillz\infrastructure\mapper;

use pocketmine\math\Vector3;
use worldedit\xchillz\domain\shared\Position;

final class PmmpVectorMapper
{
    public static function fromVector3(Vector3 $vector): Position
    {
        return new Position(
            (int) $vector->x,
            (int) $vector->y,
            (int) $vector->z
        );
    }

    public static function toVector3(Position $position): Vector3
    {
        return new Vector3(
            $position->getX(),
            $position->getY(),
            $position->getZ()
        );
    }
}
