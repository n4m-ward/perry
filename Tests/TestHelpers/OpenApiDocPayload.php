<?php

namespace Tests\TestHelpers;

class OpenApiDocPayload
{
    public static function withDefaultBody(string $paths): string
    {
        return <<<YAML
---
openapi: 3.0.0
servers:
- description: Server 1
  url: https://server1.com
- description: Server 2
  url: https://server2.com
info:
  version: 1.0.0
  title: Example server title
  description: Example server description
  contact:
    email: test@example.com
  termsOfService: https://example.com/terms-of-service
externalDocs:
  description: Find more info here
  url: https://example.com/external-docs
tags:
- name: Tag 1
  description: Tag 1 description
  externalDocs:
    description: Find more info here
    url: https://example.com/external-docs
$paths
components:
  securitySchemes:
    BearerToken:
      type: http
      in: header
      name: Authorization
      scheme: bearer
...

YAML;
    }
}