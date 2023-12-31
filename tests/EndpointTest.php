<?php

// use Laravel\Lumen\Testing\DatabaseMigrations;
// use Laravel\Lumen\Testing\DatabaseTransactions;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EndpointTest extends TestCase
{
    // /**
    //  * A basic test example.
    //  *
    //  * @return void
    //  */
    // public function testExample()
    // {
    //     $this->assertTrue(true);
    // }

    /**
     * unknown route shoult return 404
     *
     * @return void
     */
    public function test_unknown_path_should_404()
    {
        $request = $this->get('/any/random/endpoint');

        $request->response->assertJson([
            'error' => true,
        ])->assertStatus(404);
    }

    /**
     * get version sholud return lumen version
     *
     * @return void
     */
    public function test_get_version()
    {

        $this->get(route('v1.version'));

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );

    }

    /**
     * get ping should return pong
     *
     * @return void
     */
    public function test_ping_should_return_pong()
    {

        $request = $this->get(route('v1.ping'));

        $request->response->assertSee('pong')->assertStatus(200);

    }
}
