<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Collector;

use SoftWax\HealthCheck\Model\Check;

interface CheckCollectorInterface
{
    public function collect(): Check;
}
