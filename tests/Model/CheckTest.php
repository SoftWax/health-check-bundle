<?php

declare(strict_types=1);

namespace SoftWaxTests\HealthCheck\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SoftWax\HealthCheck\Model\Check;
use SoftWax\HealthCheck\Model\Component;
use SoftWax\HealthCheck\Model\ComponentTypeEnum;
use SoftWax\HealthCheck\Model\StatusEnum;

class CheckTest extends TestCase
{
    /**
     * @param Component[] $components
     */
    #[DataProvider('invalidArgumentsDataProvider')]
    public function testInvalidArguments(?string $componentName, ?string $measurementName, array $components): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Check($componentName, $measurementName, $components);
    }

    public static function invalidArgumentsDataProvider(): iterable
    {
        $component = new Component(
            'e1b1f034',
            ComponentTypeEnum::COMPONENT,
            StatusEnum::PASS,
            new \DateTimeImmutable(),
        );

        yield [':', null, [$component]];
        yield ['  q:', null, [$component]];
        yield ['  q', ':', [$component]];
        yield ['  q', ':  qa', [$component]];
        yield ['  q', '  qa', []];
    }

    #[DataProvider('keyDataProvider')]
    public function testGetKey(?string $componentName, ?string $measurementName, ?string $expectedOutput): void
    {
        $check = new Check(
            $componentName,
            $measurementName,
            [new Component('e1b1f034', ComponentTypeEnum::COMPONENT, StatusEnum::PASS, new \DateTimeImmutable())],
        );

        self::assertSame($expectedOutput, $check->getKey());
    }

    public static function keyDataProvider(): iterable
    {
        yield [null, null, null];
        yield ['', '', null];
        yield [' ', ' ', null];
        yield ['test ', ' m e ', 'test:me'];
        yield ['tes t ', null, 'test:'];
        yield [null, 'm E', ':me'];
    }
}
