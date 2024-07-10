<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\Model;

enum StatusEnum: string
{
    /* healthy */
    case PASS = 'pass';
    /* unhealthy */
    case FAIL = 'fail';
    /* healthy, with some concerns */
    case WARN = 'warn';
}
