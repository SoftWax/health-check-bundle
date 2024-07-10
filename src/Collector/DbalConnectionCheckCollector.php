<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Collector;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\Persistence\ConnectionRegistry;
use SoftWax\HealthCheck\Model\Check;
use SoftWax\HealthCheck\Model\Component;
use SoftWax\HealthCheck\Model\ComponentTypeEnum;
use SoftWax\HealthCheck\Model\StatusEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class DbalConnectionCheckCollector implements CheckCollectorInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine')]
        private ConnectionRegistry $connectionRegistry,
    ) {
    }

    public function collect(): Check
    {
        $components = [];
        foreach ($this->connectionRegistry->getConnections() as $connectionName => $connection) {
            if (!$connection instanceof Connection) {
                continue;
            }

            $connectionName = $this->parseConnectionName((string)$connectionName);

            try {
                $connection->executeQuery(
                    $connection->getDatabasePlatform()->getDummySelectSQL(),
                );

                $components[] = new Component(
                    $connectionName,
                    ComponentTypeEnum::DATASTORE,
                    StatusEnum::PASS,
                    new \DateTimeImmutable(),
                );
            } catch (DBALException) {
                $components[] = new Component(
                    $connectionName,
                    ComponentTypeEnum::DATASTORE,
                    StatusEnum::FAIL,
                    new \DateTimeImmutable(),
                );
            }
        }

        return new Check('dbal', 'connections', $components);
    }

    /**
     * @return non-empty-string
     */
    private function parseConnectionName(string $connectionName): string
    {
        if ($connectionName !== '') {
            return $connectionName;
        }

        // should not be possible, but just in case of empty name - generate unique.
        return \bin2hex(\random_bytes(16));
    }
}
