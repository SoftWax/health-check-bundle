<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Model;

enum ComponentTypeEnum: string
{
    case COMPONENT = 'component';
    case DATASTORE = 'datastore';
    case SYSTEM = 'system';
}
