<?php

namespace Tests\OpenApiDocGenerator\Cache;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Attributes\SecurityScheme\UseSecurityScheme;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\OpenApiDocGenerator\Cache\FindUsedSecurityScheme;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveDocumentationAfterTests;

#[SecurityScheme(securityScheme: 'BearerToken', type: 'http', in: 'header', name: 'Authorization', scheme: 'bearer')]
class FindUsedSecuritySchemeTest extends BaseTestCase
{
    use RemoveDocumentationAfterTests;

    public function test_shouldFindNoUsedSecurityScheme(): void
    {
        $usedSecurityScheme = (new FindUsedSecurityScheme())->execute();

        $this->assertEmpty($usedSecurityScheme);
    }

    #[UseSecurityScheme('invalid')]
    public function test_shouldThrowException_whenUsingNotImplementedSecurityScheme(): void
    {
        $this->expectException(PerryAttributeNotFoundException::class);
        $this->expectExceptionMessage("SecurityScheme [invalid] was not implemented");

        (new FindUsedSecurityScheme())->execute();

        $this->fail('PerryAttributeNotFoundException should be thrown');
    }

    #[UseSecurityScheme('BearerToken')]
    public function test_shouldFindSecurityScheme(): void
    {
        $usedSecurityScheme = (new FindUsedSecurityScheme())->execute();

        $this->assertCount(1, $usedSecurityScheme);
        $this->assertEquals('BearerToken', $usedSecurityScheme[0]->securityScheme);
        $this->assertEmpty($usedSecurityScheme[0]->scopes);
    }
}
