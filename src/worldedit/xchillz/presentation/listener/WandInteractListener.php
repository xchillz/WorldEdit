<?php

declare(strict_types=1);

namespace worldedit\xchillz\presentation\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use worldedit\xchillz\domain\session\SessionRepository;
use worldedit\xchillz\infrastructure\mapper\PmmpVectorMapper;

final class WandInteractListener implements Listener
{
    const WAND_ITEM_ID = ItemIds::WOODEN_AXE;

    /** @var SessionRepository */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getId() !== self::WAND_ITEM_ID) {
            return;
        }

        $block = $event->getBlock();
        if ($block === null) {
            return;
        }

        $session = $this->sessionRepository->get($player->getName());
        $position = PmmpVectorMapper::fromVector3(
            new Vector3(
                $block->getX(),
                $block->getY(),
                $block->getZ()
            )
        );
        $worldName = $player->getLevel()->getName();

        $action = $event->getAction();

        if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            $session->getSelection()->setFirstPosition($position);
            $session->getSelection()->setWorldName($worldName);
            $player->sendMessage(
                TextFormat::GREEN .
                    "First position: " .
                    TextFormat::WHITE .
                    $position->getX() .
                    ", " .
                    $position->getY() .
                    ", " .
                    $position->getZ()
            );
            $event->setCancelled();
        } elseif ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $session->getSelection()->setSecondPosition($position);
            $session->getSelection()->setWorldName($worldName);
            $player->sendMessage(
                TextFormat::GREEN .
                    "Second position: " .
                    TextFormat::WHITE .
                    $position->getX() .
                    ", " .
                    $position->getY() .
                    ", " .
                    $position->getZ()
            );
            $event->setCancelled();
        }
    }
}
