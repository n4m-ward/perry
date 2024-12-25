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
        $output = $this->generateRootInfo();

        Storage::saveSwaggerDoc($output);
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

        return $output;
    }
}