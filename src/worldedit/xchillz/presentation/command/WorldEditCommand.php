<?php

declare(strict_types=1);

namespace worldedit\xchillz\presentation\command;

use Exception;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use worldedit\xchillz\application\usecase\CopyUseCase;
use worldedit\xchillz\application\usecase\PasteUseCase;
use worldedit\xchillz\application\usecase\SetBlocksUseCase;
use worldedit\xchillz\application\usecase\UndoUseCase;
use worldedit\xchillz\domain\operation\BatchConfig;
use worldedit\xchillz\domain\session\SessionRepository;
use worldedit\xchillz\infrastructure\mapper\PmmpVectorMapper;

final class WorldEditCommand extends Command
{
    /** @var SessionRepository */
    private $sessionRepository;
    /** @var SetBlocksUseCase */
    private $setBlocksUseCase;
    /** @var CopyUseCase */
    private $copyUseCase;
    /** @var PasteUseCase */
    private $pasteUseCase;
    /** @var UndoUseCase */
    private $undoUseCase;

    public function __construct(
        SessionRepository $sessionRepository,
        SetBlocksUseCase $setBlocksUseCase,
        CopyUseCase $copyUseCase,
        PasteUseCase $pasteUseCase,
        UndoUseCase $undoUseCase
    ) {
        parent::__construct("we", "WorldEdit commands", "/we <set|copy|paste|undo|pos1|pos2> [args]");
        $this->sessionRepository = $sessionRepository;
        $this->setBlocksUseCase = $setBlocksUseCase;
        $this->copyUseCase = $copyUseCase;
        $this->pasteUseCase = $pasteUseCase;
        $this->undoUseCase = $undoUseCase;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if (!($sender instanceof Player)) {
            $sender->sendMessage("This command can only run in-game.");
            return;
        }

        if (count($args) === 0) {
            $sender->sendMessage(
                TextFormat::RED .
                    "Usage: " .
                    TextFormat::WHITE .
                    $this->getUsage()
            );
            return;
        }

        $subcommand = strtolower($args[0]);

        try {
            switch ($subcommand) {
                case "set":
                    $this->handleSet($sender, $args);
                    break;
                case "copy":
                    $this->handleCopy($sender);
                    break;
                case "paste":
                    $this->handlePaste($sender, $args);
                    break;
                case "undo":
                    $this->handleUndo($sender);
                    break;
                case "pos1":
                    $this->handlePos1($sender);
                    break;
                case "pos2":
                    $this->handlePos2($sender);
                    break;
                default:
                    $sender->sendMessage(
                        TextFormat::RED .
                            "Unknown subcommand: " .
                            TextFormat::WHITE .
                            $subcommand
                    );
                    break;
            }
        } catch (Exception $e) {
            $sender->sendMessage(
                TextFormat::RED .
                    "Error: " .
                    $e->getMessage()
            );
        }

        return;
    }

    private function handleSet(Player $sender, array $args)
    {
        if (!isset($args[1])) {
            $sender->sendMessage(
                TextFormat::RED .
                    "Usage: " .
                    TextFormat::WHITE .
                    "/we set <blockId> [meta] [batchSize]"
            );
            return;
        }

        $blockId = (int) $args[1];
        $blockMeta = isset($args[2]) ? (int) $args[2] : 0;
        $batchSize = isset($args[3]) ? (int) $args[3] : BatchConfig::default()->getSize();

        $operation = $this->setBlocksUseCase->execute($sender->getName(), $blockId, $blockMeta, $batchSize);

        $sender->sendMessage(
            TextFormat::GREEN .
                "Operation started with id: " .
                TextFormat::WHITE .
                $operation->getId()
        );
    }

    private function handleCopy(Player $sender)
    {
        $this->copyUseCase->execute($sender->getName());

        $sender->sendMessage(
            TextFormat::GREEN .
                "Selection copied."
        );
    }

    private function handlePaste(Player $sender, array $args)
    {
        $batchSize = isset($args[1]) ? (int) $args[1] : BatchConfig::default()->getSize();
        $target = PmmpVectorMapper::fromVector3($this->playerAsVector3($sender));

        $operation = $this->pasteUseCase->execute($sender->getName(), $target, $batchSize);

        $sender->sendMessage(
            TextFormat::GREEN .
                "Paste started with id: " .
                TextFormat::WHITE .
                $operation->getId()
        );
    }

    private function handleUndo(Player $sender)
    {
        $this->undoUseCase->execute($sender->getName());
        $sender->sendMessage(
            TextFormat::GREEN .
                "Last operation undone."
        );
    }

    private function handlePos1(Player $sender)
    {
        $session = $this->sessionRepository->get($sender->getName());
        $position = PmmpVectorMapper::fromVector3($this->playerAsVector3($sender));

        $session->getSelection()->setFirstPosition($position);
        $session->getSelection()->setWorldName($sender->getLevel()->getName());

        $sender->sendMessage(
            TextFormat::GREEN .
                "First position set"
        );
    }

    private function handlePos2(Player $sender)
    {
        $session = $this->sessionRepository->get($sender->getName());
        $position = PmmpVectorMapper::fromVector3($this->playerAsVector3($sender));

        $session->getSelection()->setSecondPosition($position);
        $session->getSelection()->setWorldName($sender->getLevel()->getName());

        $sender->sendMessage(
            TextFormat::GREEN .
                "Second position set"
        );
    }

    private function playerAsVector3(Player $player)
    {
        return new Vector3(
            $player->getFloorX(),
            $player->getFloorY(),
            $player->getFloorZ()
        );
    }
}
