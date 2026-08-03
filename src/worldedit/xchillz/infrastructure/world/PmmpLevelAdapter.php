<?php

declare(strict_types=1);

namespace worldedit\xchillz\infrastructure\world;

use pocketmine\block\Block;
use pocketmine\Server;
use worldedit\xchillz\application\port\WorldReader;
use worldedit\xchillz\application\port\WorldWriter;
use worldedit\xchillz\domain\operation\BlockChange;
use worldedit\xchillz\domain\shared\Position;
use worldedit\xchillz\infrastructure\mapper\PmmpVectorMapper;

final class PmmpLevelAdapter implements WorldReader, WorldWriter
{
    /** @var Server */
    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getBlockAt(string $worldName, Position $position): BlockChange
    {
        $level = $this->server->getLevelByName($worldName);
        if ($level === null) {
            throw new \Exception("World not loaded: {$worldName}");
        }

        $vector = PmmpVectorMapper::toVector3($position);
        $block = $level->getBlock($vector);

        return new BlockChange($position, $block->getId(), $block->getDamage());
    }

    public function setBlock(string $worldName, BlockChange $change)
    {
        $level = $this->server->getLevelByName($worldName);
        if ($level === null) {
            throw new \Exception("World not loaded: {$worldName}");
        }

        $vector = PmmpVectorMapper::toVector3($change->getPosition());
        $block = Block::get($change->getBlockId(), $change->getBlockMeta());

        $level->setBlock($vector, $block, false, false);
    }
}
