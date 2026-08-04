<?php

declare(strict_types=1);

namespace worldedit\xchillz\infrastructure\scheduler;

use pocketmine\plugin\Plugin;
use worldedit\xchillz\application\port\ProgressNotifier;
use worldedit\xchillz\application\port\TaskScheduler;
use worldedit\xchillz\application\port\WorldWriter;
use worldedit\xchillz\domain\operation\BlockOperation;

final class PmmpTaskScheduler implements TaskScheduler
{
    const BATCH_OPERATION_TICK_INTERVAL = 1;
    /** @var Plugin */
    private $plugin;
    /** @var WorldWriter */
    private $worldWriter;
    /** @var ProgressNotifier */
    private $notifier;

    public function __construct(Plugin $plugin, WorldWriter $worldWriter, ProgressNotifier $notifier)
    {
        $this->plugin = $plugin;
        $this->worldWriter = $worldWriter;
        $this->notifier = $notifier;
    }

    public function schedule(BlockOperation $operation, string $playerName)
    {
        $task = new BatchOperationTask($operation, $playerName, $this->worldWriter, $this->notifier);

        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask($task, self::BATCH_OPERATION_TICK_INTERVAL);
    }
}
