<?php

namespace TimoKoerber\LaravelOneTimeOperations\Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase;
use TimoKoerber\LaravelOneTimeOperations\Providers\OneTimeOperationsServiceProvider;

abstract class OneTimeOperationCase extends TestCase
{
    protected  const TEST_OPERATION_NAME = 'xxxx_xx_xx_xxxxxx_foo_bar';

    protected const TEST_FILE_DIRECTORY = 'tests/files';

    protected const TEST_TABLE_NAME = 'operations';

    protected const TEST_DATETIME = '2015-10-21 07:28:00';

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Carbon::setTestNow(self::TEST_DATETIME);
    }

    protected function getPackageProviders($app): array
    {
        return [
            OneTimeOperationsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup directory to provide test files
        $app['config']->set('one-time-operations.directory', '../../../../'.self::TEST_FILE_DIRECTORY);
        $app['config']->set('one-time-operations.table', self::TEST_TABLE_NAME);
        $app['config']->set('queue.default', 'database');
    }

    protected function deleteFileDirectory()
    {
        File::deleteDirectory(self::TEST_FILE_DIRECTORY);
    }

    protected function mockFileDirectory()
    {
        File::copyDirectory('tests/resources', self::TEST_FILE_DIRECTORY);
    }
}
