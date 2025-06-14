<?php

namespace Perry\OpenApiDocGenerator\OpenApi;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Files\Storage;

class GetSecuritySchemesFromCache
{
    public function execute(): array
    {
        $securitySchemes = Storage::getSecuritySchemesOrEmpty();
        if(empty($securitySchemes)) {
            return [];
        }

        return $this->mapSecurityScheme($securitySchemes);
    }

    /**
     * @param SecurityScheme[] $securitySchemes
     */
    private function mapSecurityScheme(array $securitySchemes): array
    {
        $output = [];
        foreach ($securitySchemes as $securityScheme) {
            $output[$securityScheme->securityScheme] = array_filter([
                'type' => $securityScheme->type,
                'in' => $securityScheme->in,
                'name' => $securityScheme->name,
                'scheme' => $securityScheme->scheme
            ]);
        }
        return $output;
    }
}
