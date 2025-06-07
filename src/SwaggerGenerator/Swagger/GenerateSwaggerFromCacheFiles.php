<?php

namespace Perry\SwaggerGenerator\Swagger;

use Perry\Exceptions\PerryStorageException;
use Perry\Files\Storage;

class GenerateSwaggerFromCacheFiles
{
    /**
     * @throws PerryStorageException
     */
    public function execute(): void
    {
        try {
            $output = $this->generateRootInfo();

            $requestFolder = Storage::loadRequestFolder();
            $output['paths'] = $this->parseFoldersToRequestDto($requestFolder);
            $components = $this->getComponents();

            if (!empty($components)) {
                $output['components'] = $components;
            }

            Storage::saveSwaggerDoc($output);
        } finally {
            Storage::deleteCacheFolder();
        }
    }

    private function parseFoldersToRequestDto(array $folders): array
    {
        $output = [];
        $parseEndpoint = new ParseEndpointToSwaggerDocumentation();

        foreach ($folders as $folder) {
            $endpoint = $this->getEndpointFromFolderName($folder);
            $output[$endpoint] = $parseEndpoint->execute($folder);
        }
        return $output;
    }

    /**
     * @throws PerryStorageException
     */
    private function generateRootInfo(): array
    {
        $rootInfo = Storage::getRootInfo();

        $output = ['openapi' => '3.0.0'];

        if($rootInfo->servers) {
            foreach($rootInfo->servers->servers as $server) {
                $output['servers'][] = [
                    'description' => $server->description,
                    'url' => $server->url,
                ];
            }
        }
        $output['info'] = [
            'version' => $rootInfo->info->version,
            'title' => $rootInfo->info->title,
            'description' => $rootInfo->info->description,
        ];

        if($rootInfo->info->contactEmail) {
            $output['info']['contact'] = [
                'email' => $rootInfo->info->contactEmail,
            ];
        }
        if($rootInfo->info->termsOfService) {
            $output['info']['termsOfService'] = $rootInfo->info->termsOfService;
        }
        if($rootInfo->info->externalDocs) {
            $output['externalDocs'] = [
                'description' => $rootInfo->info->externalDocs->description,
                'url' => $rootInfo->info->externalDocs->url,
            ];
        }

        return $output;
    }

    private function getComponents(): array
    {
        $output = [];
        $securitySchemes = (new GenerateSecurityScheme)->execute();
        if(!empty($securitySchemes)) {
            $output['securitySchemes'] = $securitySchemes;
        }

        return $output;
    }

    private function getEndpointFromFolderName(string $folder): string
    {
        $endpoint = str_replace('_', '/', $folder);
        if (!str_starts_with($endpoint, '/')) {
            $endpoint = '/' . $endpoint;
        }
        return $endpoint;
    }
}
