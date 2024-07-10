<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\DependencyInjection;

use SoftWax\HealthCheck\Collector\CheckCollectorInterface;
use SoftWax\HealthCheck\Collector\DbalConnectionCheckCollector;
use SoftWax\HealthCheck\Http\HealthCheckAction;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SoftWaxHealthCheckExtension extends Extension
{
    private const string CHECK_COLLECTOR_TAG_NAME = 'softwax.health_check_collector';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $env = $container->getParameter('kernel.environment');

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
            \is_string($env) ? $env : null,
        );

        $this->loadConfigFile('services.yaml', $loader);

        $container
            ->registerForAutoconfiguration(CheckCollectorInterface::class)
            ->addTag(self::CHECK_COLLECTOR_TAG_NAME);

        $container
            ->getDefinition(HealthCheckAction::class)
            ->replaceArgument(0, new TaggedIteratorArgument(self::CHECK_COLLECTOR_TAG_NAME));

        if ($config['native_check_collectors']['dbal_connection'] === false) {
            $container->removeDefinition(DbalConnectionCheckCollector::class);
        }
    }

    private function loadConfigFile(string $file, LoaderInterface $loader): void
    {
        try {
            $loader->load($file);
        } catch (\Exception $e) {
            throw new \LogicException(\sprintf('Failed to load %s %s', $this->getAlias(), $file), 0, $e);
        }
    }
}
