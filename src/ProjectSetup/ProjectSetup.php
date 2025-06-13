<?php

namespace Perry\ProjectSetup;

use Perry\Exceptions\PerryException;
use Perry\ProjectExecutionMode;
use Perry\UnitTest\UnitTestConfigLoader;

class ProjectSetup
{
    /**
     * @throws PerryException
     */
    public function executeAndExitIfNecessary(): void
    {
        if ($this->alreadyHaveSetup()) {
            return;
        }

        $this->generateDefaultPerryConfigFile();
        $this->generatePerryOutputFolder();
        $this->generatePerryBaseTestCase();

        exit(0);
    }

    /**
     * @throws PerryException
     */
    private function alreadyHaveSetup(): bool
    {
        $unitTestConfig = UnitTestConfigLoader::load();

        return !is_null($unitTestConfig);
    }

    private function generateDefaultPerryConfigFile(): void
    {
        echo "✅ Generating default project config file \n\n";

        $defaultUnitTestConfig = <<<JSON
{
    "testsFolderPath": "/tests/Perry",
    "testExecutorPath": "/vendor/bin/phpunit",
    "swaggerOutputPath": "/perry_output/swagger",
    "cacheOutputPath": "/perry_output/cache"
}
JSON;

        file_put_contents($this->getProjectRootFolder() . '/perry.json', $defaultUnitTestConfig);
    }

    private function getProjectRootFolder(): string
    {
        return ProjectExecutionMode::load()->getRootFolder();
    }

    private function generatePerryOutputFolder(): void
    {
        $perryOutputFolder = $this->getProjectRootFolder() . '/perry_output';
        if (!is_dir($perryOutputFolder)) {
            mkdir($perryOutputFolder);
        }
    }

    private function generatePerryBaseTestCase(): void
    {
        $baseTestCase = <<<PHP
<?php

namespace Tests\Perry;

use Perry\Attributes\ExternalDocs;
use Perry\Attributes\Info;
use Perry\Attributes\Server;
use Perry\Attributes\Servers;
use Perry\PerryHttp\PerryHttpRequest;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

#[Servers(
    new Server(description: 'Example server local', url: 'http://localhost:8000')
)]
#[Info(
    version: '1.0.0',
    title: 'Example server title',
    description: 'Example server description',
    contactEmail: 'test@example.com',
    termsOfService: 'https://example.com/terms-of-service',
    externalDocs: new ExternalDocs(
        url: 'https://example.com/external-docs',
        description: 'Find more info here',
    ),
)]
abstract class BaseTestCase extends LaravelTestCase
{
    use PerryHttpRequest;
}

PHP;
        if(!is_dir($this->getProjectRootFolder() . '/tests')) {
            mkdir($this->getProjectRootFolder() . '/tests');
        }
        if(!is_dir($this->getProjectRootFolder() . '/tests/Perry')) {
            mkdir($this->getProjectRootFolder() . '/tests/Perry');
        }
        $baseTestCaseFile = $this->getProjectRootFolder() . '/tests/Perry/BaseTestCase.php';

        file_put_contents($baseTestCaseFile, $baseTestCase);
    }
}
