<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Model;

final readonly class Check
{
    private ?string $key;

    /**
     * @var non-empty-list<Component>
     */
    private array $components;

    /**
     * @param list<Component> $components
     * @throws \InvalidArgumentException
     */
    public function __construct(?string $componentName, ?string $measurementName, array $components)
    {
        if (\str_contains((string)$componentName, ':') || \str_contains((string)$measurementName, ':')) {
            throw new \InvalidArgumentException('componentName and measurementName cannot contain a colon');
        }

        if ($components === []) {
            throw new \InvalidArgumentException('Components array must contain at least one element');
        }

        $key = \sprintf(
            '%s:%s',
            \trim((string)\preg_replace('/\s+/', '', (string)$componentName)),
            \trim((string)\preg_replace('/\s+/', '', (string)$measurementName)),
        );
        $this->key = $key === ':' ? null : \mb_strtolower($key);
        $this->components = $components;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @return non-empty-list<Component>
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    public function hasPassed(): bool
    {
        foreach ($this->components as $components) {
            if ($components->getStatus() === StatusEnum::FAIL) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int|string, array>
     */
    public function normalize(): array
    {
        $normalizedComponents = \array_map(
            static function (Component $component): array {
                return $component->normalize();
            },
            $this->components,
        );

        return $this->key !== null ? [$this->key => $normalizedComponents] : [$normalizedComponents];
    }
}
