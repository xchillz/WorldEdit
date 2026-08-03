<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\session;

use worldedit\xchillz\domain\clipboard\Clipboard;
use worldedit\xchillz\domain\operation\BlockOperation;
use worldedit\xchillz\domain\selection\Selection;

final class EditSession
{
    /** @var string */
    private $playerName;
    /** @var Selection */
    private $selection;
    /** @var ?Clipboard */
    private $clipboard;
    /** @var BlockOperation[] */
    private $history;
    /** @var int */
    private $historyPointer;

    public function __construct(string $playerName, Selection $selection)
    {
        $this->playerName = $playerName;
        $this->selection = $selection;
        $this->history = [];
        $this->historyPointer = -1;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setClipboard(Clipboard $clipboard)
    {
        $this->clipboard = $clipboard;
    }

    public function getClipboard()
    {
        return $this->clipboard;
    }

    public function pushHistory(BlockOperation $blockOperation)
    {
        $this->history = array_slice($this->history, 0, $this->historyPointer + 1);
        $this->history[] = $blockOperation;
        $this->historyPointer++;
    }

    public function moveHistoryPointerBack(): bool
    {
        if ($this->historyPointer < 0) {
            return false;
        }

        $this->historyPointer--;
        return true;
    }

    public function moveHistoryPointerForward(): bool
    {
        if ($this->historyPointer >= count($this->history) - 1) {
            return false;
        }

        $this->historyPointer++;
        return true;
    }

    public function getLastOperation()
    {
        if ($this->historyPointer < 0) {
            return null;
        }

        return $this->history[$this->historyPointer];
    }
}
