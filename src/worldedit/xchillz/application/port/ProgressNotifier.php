<?php

declare(strict_types=1);

namespace worldedit\xchillz\application\port;

interface ProgressNotifier
{
    public function notifyProgress(string $playerName, float $progress);
    public function notifyComplete(string $playerName, int $totalBlocks);
}
