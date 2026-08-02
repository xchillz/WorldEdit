<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\port;

use worldedit\xchillz\domain\operation\BlockOperation;

interface TaskScheduler
{
    public function schedule(BlockOperation $blockOperation, string $playerName);
}
