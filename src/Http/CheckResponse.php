<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Http;

use SoftWax\HealthCheck\Collector\CheckCollectorInterface;
use SoftWax\HealthCheck\Model\StatusEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CheckResponse extends JsonResponse
{
    /**
     * @param iterable<CheckCollectorInterface> $checkCollectors
     */
    public static function createFromCollectors(iterable $checkCollectors): CheckResponse
    {
        $status = StatusEnum::PASS;
        $normalizedChecks = [];
        foreach ($checkCollectors as $checkCollector) {
            $check = $checkCollector->collect();
            if (!$check->hasPassed()) {
                $status = StatusEnum::FAIL;
            }

            $normalizedCheck = $check->normalize();
            $key = \key($normalizedCheck);

            if (\is_string($key)) {
                $normalizedChecks[$key] = $normalizedCheck[$key];
            } else {
                $normalizedChecks[] = $normalizedCheck[$key];
            }
        }

        return new self(
            [
                'status' => $status->value,
                'checks' => $normalizedChecks,
            ],
            $status === StatusEnum::PASS ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE,
            ['Content-Type' => 'application/health+json'],
        );
    }
}
