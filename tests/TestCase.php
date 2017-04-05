<?php

namespace TheLHC\CustomAuth\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TestCase extends BaseTestCase
{

    /**
     * Schema Helpers.
     */
    protected function schema()
    {
        return $this->connection()->getSchemaBuilder();
    }

    protected function connection()
    {
        return Eloquent::getConnectionResolver()->connection();
    }

    public static function setUpBeforeClass()
    {
        if (file_exists(__DIR__.'/../.env')) {
            $dotenv = new Dotenv(__DIR__.'/../');
            $dotenv->load();
        }
    }

    public function setUp()
    {
        parent::setUp();
        $this->schema = $this->app['db']->connection()->getSchemaBuilder();
        $this->runTestMigrations();
        $this->beforeApplicationDestroyed(function () {
            $this->rollbackTestMigrations();
        });
        Eloquent::unguard();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //$dotenv = new Dotenv(__DIR__);
        //$dotenv->load();
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
        $app['config']->set('auth.providers.users.model', \TheLHC\CustomAuth\Tests\User::class);
        $app['config']->set('auth.providers.users.driver', 'custom-auth');
        $app['config']->set('auth.guards.web.driver', 'custom-auth');
    }

    /**
     * Run migrations for tables only used for testing purposes.
     *
     * @return void
     */
    protected function runTestMigrations()
    {
        if (! $this->schema->hasTable('users')) {
            $this->schema->create('users', function ($table) {
                $table->increments('id');
                $table->string('email');
                $table->string('name');
                $table->string('password');
                $table->string('salt');
            });
        }
    }

    /**
     * Rollback migrations for tables only used for testing purposes.
     *
     * @return void
     */
    protected function rollbackTestMigrations()
    {
        $this->schema->drop('users');
    }

    protected function getPackageProviders($app)
    {
        return [
            \TheLHC\CustomAuth\CustomAuthServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            //'Alias' => 'fullpath'
        ];
    }


}
