<?php

declare(strict_types=1);

namespace worldedit\xchillz\infrastructure\notifier;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use worldedit\xchillz\application\port\ProgressNotifier;

final class PmmpProgressNotifier implements ProgressNotifier
{
    /** @var Server */
    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function notifyProgress(string $playerName, float $progress)
    {
        $player = $this->server->getPlayerExact($playerName);
        if ($player === null || !$player->isOnline()) {
            return;
        }

        $player->sendPopup(
            TextFormat::GREEN .
                "Progress: " .
                TextFormat::WHITE .
                round($progress * 100) .
                " percent"
        );
    }

    public function notifyComplete(string $playerName, int $totalBlocks)
    {
        $player = $this->server->getPlayerExact($playerName);
        if ($player === null || !$player->isOnline()) {
            return;
        }

        $player->sendMessage(
            TextFormat::GREEN .
                "Operation completed: " .
                TextFormat::WHITE .
                $totalBlocks .
                TextFormat::GREEN .
                " blocks."
        );
    }
}
