<?php

namespace Tests\Unit;

use Tests\TestCase;

class DatabaseConfigurationTest extends TestCase
{
    public function test_phpunit_uses_the_dedicated_testing_database(): void
    {
        $this->assertSame(
            'testing',
            app()->environment()
        );

        $this->assertSame(
            'pgsql',
            config('database.default')
        );

        $this->assertSame(
            'fams_testing',
            config(
                'database.connections.pgsql.database'
            )
        );
    }
}