<?php

declare(strict_types=1);

namespace SoftWaxTests\HealthCheck\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SoftWax\HealthCheck\Collector\DbalConnectionCheckCollector;
use SoftWax\HealthCheck\DependencyInjection\SoftWaxHealthCheckExtension;

class SoftWaxHealthCheckExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SoftWaxHealthCheckExtension()];
    }

    public function testContainerWithDefaultConfigurationValues(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->load([]);
        $this->compile();

        $this->assertContainerBuilderNotHasService(DbalConnectionCheckCollector::class);
    }

    public function testContainerWithNotDefaultConfigurationValues(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->load(
            [
                'native_check_collectors' => [
                    'dbal_connection' => true,
                ],
            ],
        );
        $this->compile();

        $this->assertContainerBuilderHasService(DbalConnectionCheckCollector::class);
    }
}
