<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\operation;

final class OperationStatus
{
    const PENDING = 0;
    const RUNNING = 1;
    const DONE = 2;
    const CANCELLED = 3;
    const FAILED = 4;
}
