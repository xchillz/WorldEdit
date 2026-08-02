<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\port;

use worldedit\xchillz\domain\operation\BlockChange;

interface WorldWriter
{
    public function setBlock(string $worldName, BlockChange $blockChange);
}
