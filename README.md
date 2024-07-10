# SoftWax Health Check Bundle

Based on https://datatracker.ietf.org/doc/html/draft-inadarei-api-health-check

## Installation

`composer require softwax/health-check-bundle`

## Configuration

```yaml
# routes.yaml
health_check:
    path: /health
    controller: SoftWax\HealthCheck\Http\HealthCheckAction
    methods: GET
```
