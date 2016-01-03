<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommandsTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testTrackingUserCommand()
    {
        Veer\Jobs\TrackingUser::run();
    }
}
