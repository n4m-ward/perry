<?php

namespace Tests\OpenApiDocGenerator\Cache;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Files\Storage;
use Perry\OpenApiDocGenerator\Cache\SaveOpenApiSecuritySchemeOnCacheIfExists;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveSwaggerAfterTests;

#[SecurityScheme(securityScheme: 'BearerToken', type: 'http', in: 'header', name: 'Authorization', scheme: 'bearer')]
class SaveOpenApiSecuritySchemeOnCacheIfExistsTest extends BaseTestCase
{
    use RemoveSwaggerAfterTests;

    public function test_shouldSaveSecurityScheme(): void
    {
        (new SaveOpenApiSecuritySchemeOnCacheIfExists())->execute();
        $securitySchemes = Storage::getSecuritySchemesOrEmpty();

        $this->assertCount(1, $securitySchemes);
        $this->assertEquals('BearerToken', $securitySchemes[0]->securityScheme);
        $this->assertEquals('http', $securitySchemes[0]->type);
        $this->assertEquals('header', $securitySchemes[0]->in);
        $this->assertEquals('Authorization', $securitySchemes[0]->name);
        $this->assertEquals('bearer', $securitySchemes[0]->scheme);
    }

    public function test_shouldNotDuplicateSecurityScheme(): void
    {
        Storage::saveSecuritySchemes([new SecurityScheme(securityScheme: 'BearerToken', type: 'http', in: 'header', name: 'Authorization', scheme: 'bearer')]);
        (new SaveOpenApiSecuritySchemeOnCacheIfExists())->execute();
        $securitySchemes = Storage::getSecuritySchemesOrEmpty();

        $this->assertCount(1, $securitySchemes);
    }
}
