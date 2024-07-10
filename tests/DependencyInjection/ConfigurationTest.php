<?php

declare(strict_types=1);

namespace SoftWaxTests\HealthCheck\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use SoftWax\HealthCheck\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    public function testDefaultConfigurationValues(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            [
                'native_check_collectors' => [
                    'dbal_connection' => false,
                ],
            ],
        );
    }

    public function testModifiedConfigurationValues(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                'softwax_health_check' => [
                    'native_check_collectors' => [
                        'dbal_connection' => true,
                    ],
                ],
            ],
            [
                'native_check_collectors' => [
                    'dbal_connection' => true,
                ],
            ],
        );
    }
}
