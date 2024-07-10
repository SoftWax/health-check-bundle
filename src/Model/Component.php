<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Model;

final readonly class Component
{
    /**
     * @param non-empty-string $componentId
     */
    public function __construct(
        private string $componentId,
        private ComponentTypeEnum $componentType,
        private StatusEnum $status,
        private \DateTimeInterface $time,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getComponentId(): string
    {
        return $this->componentId;
    }

    public function getComponentType(): ComponentTypeEnum
    {
        return $this->componentType;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @return array{
     *     'componentId': non-empty-string,
     *     'componentType': non-empty-string,
     *     'status': non-empty-string,
     *     'time': non-empty-string
     * }
     */
    public function normalize(): array
    {
        return [
            'componentId' => $this->componentId,
            'componentType' => $this->componentType->value,
            'status' => $this->status->value,
            'time' => $this->time->format(\DateTimeInterface::ATOM),
        ];
    }
}
