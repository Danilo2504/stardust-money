<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Models created during the test that must be force-deleted on tearDown.
     * Track them in creation order (children before parents); tearDown iterates
     * in reverse so FK dependents are removed before their parents.
     *
     * @var array<int, Model>
     */
    protected array $testRecords = [];

    protected function track(Model $model): Model
    {
        $this->testRecords[] = $model;

        return $model;
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->testRecords) as $model) {
            if ($model->exists) {
                $model->forceDelete();
            }
        }

        $this->testRecords = [];

        parent::tearDown();
    }

    /**
     * Force the application environment to "testing".
     *
     * The .env file is loaded with Dotenv::createMutable, which means
     * APP_ENV=local in .env can override the APP_ENV=testing set in
     * phpunit.xml. We re-assert the testing env after boot so that
     * runningUnitTests() returns true and the CSRF middleware is
     * skipped during HTTP tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->detectEnvironment(fn () => 'testing');
    }
}
