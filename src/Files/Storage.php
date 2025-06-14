<?php

namespace Perry\Files;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Attributes\Tag\Tag;
use Perry\Exceptions\PerryStorageException;
use Perry\OpenApiCache\OpenApiRootInfo;
use Perry\OpenApiDocGenerator\Cache\Dtos\TestRequestDto;
use Symfony\Component\Yaml\Yaml;

class Storage
{
    private const ROOT_INFO_DIR = 'root_info';

    public static function saveTestRootInfo(OpenApiRootInfo $rootInfo): void
    {
        $cacheFolder = self::getCacheFolder();
        $rootInfoFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR;
        if(!is_dir($rootInfoFolder)) {
            mkdir($rootInfoFolder);
        }

        self::saveFile($rootInfoFolder . '/root_info.cache', serialize($rootInfo));
    }

    public static function saveTestRequest(TestRequestDto $dto): void
    {
        $allRequestsFolder = StoragePathResolver::resolveRequestsFolder();
        $endpointRequestsFolder = str_replace('/', '_', $dto->path);
        $testFolder = join('/', [$allRequestsFolder, $endpointRequestsFolder, $dto->method]);

        self::createTestFolderIfNeeded($dto);
        self::saveFile($testFolder . '/' . $dto->statusCode, serialize($dto));
    }

    private static function createTestFolderIfNeeded(TestRequestDto $dto): void
    {
        $allRequestsFolder = StoragePathResolver::resolveRequestsFolder();
        if(!is_dir($allRequestsFolder)) {
            mkdir($allRequestsFolder);
        }
        $endpointRequestsFolder = str_replace('/', '_', $dto->path);
        $endpointRequestsFolder = $allRequestsFolder . '/'. $endpointRequestsFolder;

        if(!is_dir($endpointRequestsFolder)) {
            mkdir($endpointRequestsFolder);
        }
        if(!is_dir($endpointRequestsFolder . '/'. $dto->method)) {
            mkdir($endpointRequestsFolder . '/'. $dto->method);
        }
    }

    public static function getSingleTestRequest(string $endpoint, string $method, string $statusCode): ?TestRequestDto
    {
        $allRequestsFolder = StoragePathResolver::resolveRequestsFolder();
        $endpoint = str_replace('/', '_', $endpoint);
        $fullPath = join('/', [$allRequestsFolder, $endpoint, $method, $statusCode]);

        if(!is_file($fullPath)) {
            return null;
        }

        return unserialize(file_get_contents($fullPath));
    }

    public static function loadRequestFolder(string $subPath = ''): array
    {
        $allRequestsFolderPath = StoragePathResolver::resolveRequestsFolder() . $subPath;
        $allRequestsFolder = scandir($allRequestsFolderPath);
        unset($allRequestsFolder[0]);
        unset($allRequestsFolder[1]);

        return $allRequestsFolder;
    }

    /**
     * @throws PerryStorageException
     */
    public static function getRootInfo(): OpenApiRootInfo
    {
        $cacheFolder = self::getCacheFolder();
        $rootInfoFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR . '/root_info.cache';


        if (!is_file($rootInfoFolder)) {
            throw new PerryStorageException('No test documentation found. First create a e2e test to generate the documentation');
        }

        $rootInfoSerialized = file_get_contents($rootInfoFolder);

        if(empty($rootInfoSerialized)) {
            throw new PerryStorageException('root_info file not found on cache!');
        }

        return unserialize($rootInfoSerialized);
    }

    public static function getOpenApiDocumentation(): ?string
    {
        $docFile = StoragePathResolver::resolveDocumentationFolder() .'/output.yaml';
        if(!is_file($docFile)) {
            return null;
        }

        return file_get_contents($docFile);
    }

    public static function saveSwaggerDoc(array $docArray): void
    {
        $docYaml = yaml_emit($docArray);
        if(!is_dir(StoragePathResolver::resolveDocumentationFolder())) {
            mkdir(StoragePathResolver::resolveDocumentationFolder());
        }

        self::saveFile(StoragePathResolver::resolveDocumentationFolder() .'/output.yaml', $docYaml);
    }

    public static function saveSecuritySchemes(array $securitySchemes): void
    {
        $cacheFolder = self::getCacheFolder();
        $rootInfoFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR;
        if(!is_dir($rootInfoFolder)) {
            mkdir($rootInfoFolder);
        }

        self::saveFile($rootInfoFolder . '/security_schemes.cache', serialize($securitySchemes));
    }

    public static function saveTags(array $tags): void
    {
        $cacheFolder = self::getCacheFolder();
        $rootInfoFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR;
        if(!is_dir($rootInfoFolder)) {
            mkdir($rootInfoFolder);
        }

        self::saveFile($rootInfoFolder . '/tags.cache', serialize($tags));
    }

    /**
     * @return SecurityScheme[]
     */
    public static function getSecuritySchemesOrEmpty(): array
    {
        $cacheFolder = self::getCacheFolder();
        $securitySchemeFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR . '/security_schemes.cache';


        if (!is_file($securitySchemeFolder)) {
            return [];
        }

        $securitySchemeSerialized = file_get_contents($securitySchemeFolder);

        if(empty($securitySchemeSerialized)) {
            return [];
        }

        return unserialize($securitySchemeSerialized);
    }


    /**
     * @return Tag[]
     */
    public static function getTagsOrEmpty(): array
    {
        $cacheFolder = self::getCacheFolder();
        $tagsFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR . '/tags.cache';


        if (!is_file($tagsFolder)) {
            return [];
        }

        $tagsSerialized = file_get_contents($tagsFolder);

        if(empty($tagsSerialized)) {
            return [];
        }

        return unserialize($tagsSerialized);
    }

    private static function saveFile(string $file, string $content): void
    {
        file_put_contents($file, $content);
    }

    private static function getCacheFolder(): string
    {
        $cacheFolder = StoragePathResolver::resolveCacheFolder();

        if(!is_dir($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }
        return $cacheFolder;
    }

    public static function deleteCacheFolder(): void
    {
        $cacheFolder = StoragePathResolver::resolveCacheFolder();
        if(is_dir("$cacheFolder")) {
            self::deleteFolder("$cacheFolder");
        }
    }

    public static function deleteDocumentationFolder(): void
    {
        $swaggerFolder = StoragePathResolver::resolveDocumentationFolder();
        if(is_dir($swaggerFolder)) {
            self::deleteFolder($swaggerFolder);
        }
    }

    private static function deleteFolder(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        if (is_file($path) || is_link($path)) {
            unlink($path);
            return;
        }

        $items = scandir($path);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            self::deleteFolder($itemPath);
        }

        rmdir($path);
    }
}
