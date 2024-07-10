<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Http;

use SoftWax\HealthCheck\Collector\CheckCollectorInterface;
use Symfony\Component\HttpFoundation\Response;

final readonly class HealthCheckAction
{
    /**
     * @param iterable<CheckCollectorInterface> $checkCollectors
     */
    public function __construct(
        private iterable $checkCollectors,
    ) {
    }

    public function __invoke(): Response
    {
        return CheckResponse::createFromCollectors($this->checkCollectors);
    }
}
