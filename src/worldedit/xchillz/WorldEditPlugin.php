<?php

declare(strict_types=1);

namespace worldedit\xchillz;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use worldedit\xchillz\application\service\BatchExecutionService;
use worldedit\xchillz\application\usecase\CopyUseCase;
use worldedit\xchillz\application\usecase\PasteUseCase;
use worldedit\xchillz\application\usecase\SetBlocksUseCase;
use worldedit\xchillz\application\usecase\UndoUseCase;
use worldedit\xchillz\infrastructure\notifier\PmmpProgressNotifier;
use worldedit\xchillz\infrastructure\repository\InMemorySessionRepository;
use worldedit\xchillz\infrastructure\scheduler\PmmpTaskScheduler;
use worldedit\xchillz\infrastructure\world\PmmpLevelAdapter;
use worldedit\xchillz\presentation\command\WorldEditCommand;
use worldedit\xchillz\presentation\listener\WandInteractListener;

final class WorldEditPlugin extends PluginBase
{
    public function onEnable()
    {
        $sessionRepository = new InMemorySessionRepository();

        $worldAdapter = new PmmpLevelAdapter($this->getServer());
        $notifier = new PmmpProgressNotifier($this->getServer());

        $scheduler = new PmmpTaskScheduler($this, $worldAdapter, $worldAdapter, $notifier);

        $batchExecutionService = new BatchExecutionService($scheduler);

        $setBlocksUseCase = new SetBlocksUseCase($sessionRepository, $worldAdapter, $batchExecutionService);
        $copyUseCase = new CopyUseCase($sessionRepository, $worldAdapter);
        $pasteUseCase = new PasteUseCase($sessionRepository, $batchExecutionService);
        $undoUseCase = new UndoUseCase($sessionRepository, $batchExecutionService);

        $worldEditCommand = new WorldEditCommand(
            $sessionRepository,
            $setBlocksUseCase,
            $copyUseCase,
            $pasteUseCase,
            $undoUseCase
        );
        $this->getServer()->getCommandMap()->register("worldedit", $worldEditCommand);

        $this->getServer()->getPluginManager()->registerEvents(
            new WandInteractListener($sessionRepository),
            $this
        );

        $this->getLogger()->info(
            TextFormat::GREEN .
                "World Edit plugin loaded just fine!"
        );
    }

    public function onDisable()
    {
        $this->getLogger()->info(
            TextFormat::RED .
                "World Edit plugin is now disabled."
        );
    }
}
