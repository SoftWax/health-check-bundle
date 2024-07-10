<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck;

use SoftWax\HealthCheck\DependencyInjection\SoftWaxHealthCheckExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SoftWaxHealthCheckBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!$this->extension instanceof ExtensionInterface) {
            $this->extension = new SoftWaxHealthCheckExtension();
        }

        return $this->extension;
    }
}
