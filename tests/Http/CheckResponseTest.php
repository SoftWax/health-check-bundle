<?php

declare(strict_types=1);

namespace SoftWaxTests\HealthCheck\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SoftWax\HealthCheck\Collector\CheckCollectorInterface;
use SoftWax\HealthCheck\Http\CheckResponse;
use SoftWax\HealthCheck\Model\Check;
use SoftWax\HealthCheck\Model\Component;
use SoftWax\HealthCheck\Model\ComponentTypeEnum;
use SoftWax\HealthCheck\Model\StatusEnum;

class CheckResponseTest extends TestCase
{
    /**
     * @param CheckCollectorInterface[] $collectors
     */
    #[DataProvider('dataProvider')]
    public function testCreateFromCollectors(array $collectors, CheckResponse $expectedOutput): void
    {
        self::assertEquals($expectedOutput, CheckResponse::createFromCollectors($collectors));
    }

    public static function dataProvider(): iterable
    {
        $collector1 = static::createStub(CheckCollectorInterface::class);
        $collector1->method('collect')->willReturn(
            new Check(
                'mysql',
                'connections',
                [
                    new Component(
                        'fbbc6298',
                        ComponentTypeEnum::DATASTORE,
                        StatusEnum::PASS,
                        new \DateTimeImmutable('2021-01-17 16:30:25'),
                    ),
                ],
            ),
        );

        $collector2 = static::createStub(CheckCollectorInterface::class);
        $collector2->method('collect')->willReturn(
            new Check(
                'redis',
                'connections',
                [
                    new Component(
                        '0d2b0659',
                        ComponentTypeEnum::DATASTORE,
                        StatusEnum::PASS,
                        new \DateTimeImmutable('2020-02-10 17:21:29'),
                    ),
                    new Component(
                        '94964738',
                        ComponentTypeEnum::DATASTORE,
                        StatusEnum::WARN,
                        new \DateTimeImmutable('2020-02-11 17:23:21'),
                    ),
                ],
            ),
        );

        $collector3 = static::createStub(CheckCollectorInterface::class);
        $collector3->method('collect')->willReturn(
            new Check(
                'sqs',
                'connections',
                [
                    new Component(
                        'bac5a649',
                        ComponentTypeEnum::COMPONENT,
                        StatusEnum::FAIL,
                        new \DateTimeImmutable('2020-01-12 11:23:22'),
                    ),
                ],
            ),
        );

        yield [
            [],
            new CheckResponse(
                [
                    'status' => 'pass',
                    'checks' => [],
                ],
                200,
                ['Content-Type' => 'application/health+json'],
            ),
        ];

        yield [
            [$collector1],
            new CheckResponse(
                [
                    'status' => 'pass',
                    'checks' => [
                        'mysql:connections' => [
                            [
                                'componentId' => 'fbbc6298',
                                'componentType' => 'datastore',
                                'status' => 'pass',
                                'time' => '2021-01-17T16:30:25+00:00',
                            ],
                        ],
                    ],
                ],
                200,
                ['Content-Type' => 'application/health+json'],
            ),
        ];

        yield [
            [$collector1, $collector2],
            new CheckResponse(
                [
                    'status' => 'pass',
                    'checks' => [
                        'mysql:connections' => [
                            [
                                'componentId' => 'fbbc6298',
                                'componentType' => 'datastore',
                                'status' => 'pass',
                                'time' => '2021-01-17T16:30:25+00:00',
                            ],
                        ],
                        'redis:connections' => [
                            [
                                'componentId' => '0d2b0659',
                                'componentType' => 'datastore',
                                'status' => 'pass',
                                'time' => '2020-02-10T17:21:29+00:00',
                            ],
                            [
                                'componentId' => '94964738',
                                'componentType' => 'datastore',
                                'status' => 'warn',
                                'time' => '2020-02-11T17:23:21+00:00',
                            ],
                        ],
                    ],
                ],
                200,
                ['Content-Type' => 'application/health+json'],
            ),
        ];

        yield [
            [$collector1, $collector2, $collector3],
            new CheckResponse(
                [
                    'status' => 'fail',
                    'checks' => [
                        'mysql:connections' => [
                            [
                                'componentId' => 'fbbc6298',
                                'componentType' => 'datastore',
                                'status' => 'pass',
                                'time' => '2021-01-17T16:30:25+00:00',
                            ],
                        ],
                        'redis:connections' => [
                            [
                                'componentId' => '0d2b0659',
                                'componentType' => 'datastore',
                                'status' => 'pass',
                                'time' => '2020-02-10T17:21:29+00:00',
                            ],
                            [
                                'componentId' => '94964738',
                                'componentType' => 'datastore',
                                'status' => 'warn',
                                'time' => '2020-02-11T17:23:21+00:00',
                            ],
                        ],
                        'sqs:connections' => [
                            [
                                'componentId' => 'bac5a649',
                                'componentType' => 'component',
                                'status' => 'fail',
                                'time' => '2020-01-12T11:23:22+00:00',
                            ],
                        ],
                    ],
                ],
                503,
                ['Content-Type' => 'application/health+json'],
            ),
        ];
    }
}
