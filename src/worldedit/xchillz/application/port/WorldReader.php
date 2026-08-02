<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\port;

use worldedit\xchillz\domain\operation\BlockChange;
use worldedit\xchillz\domain\shared\Position;

interface WorldReader
{
    public function getBlockAt(string $worldName, Position $position): BlockChange;
}
