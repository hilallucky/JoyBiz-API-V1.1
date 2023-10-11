<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use app\Libraries\Core;

class UserRegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * test validate first_name properties
     *
     * @return void
     */
    public function test_validate_first_name_properties()
    {

        /* require */
        $request = $this->post('/v1/register', [
            // 'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The first_name field is required.'
        ])->assertStatus(400);

        /* minimum character >= 2 */
        $request = $this->post('/v1/register', [
            'first_name' => 'J',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The first_name must be at least 2 characters.'
        ])->assertStatus(400);

        /* maximun character <= 50 */
        $request = $this->post('/v1/register', [
            'first_name' => Core::generateRandomString(51),
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The first_name may not be greater than 50 characters.'
        ])->assertStatus(400);

    }

    /**
     * test validate last_name properties
     *
     * @return void
     */
    public function test_validate_last_name_properties()
    {

        /* require */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            // 'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The last_name field is required.'
        ])->assertStatus(400);

        /* minimum character >= 2 */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'D',
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The last_name must be at least 2 characters.'
        ])->assertStatus(400);

        /* maximun character <= 100 */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => Core::generateRandomString(101),
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The last_name may not be greater than 100 characters.'
        ])->assertStatus(400);

    }

    /**
     * test validate email properties
     *
     * @return void
     */
    public function test_validate_email_properties()
    {

        /* require */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            // 'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The email field is required.'
        ])->assertStatus(400);

        /* is email */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe.email',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The email must be a valid email address.'
        ])->assertStatus(400);

    }

    /**
     * test validate last_name properties
     *
     * @return void
     */
    public function test_validate_password_properties()
    {

        /* require */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            // 'password' => 'secret',
        ]);

        $request->response->assertJson([
            'error' => 'The password field is required.'
        ])->assertStatus(400);

        /* minimum character >= 6 */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'secre',
        ]);

        $request->response->assertJson([
            'error' => 'The password must be at least 6 characters.'
        ])->assertStatus(400);

        /* maximun character <= 100 */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => Core::generateRandomString(101),
        ]);

        $request->response->assertJson([
            'error' => 'The password may not be greater than 100 characters.'
        ])->assertStatus(400);

    }

    /**
     * test validate email properties
     *
     * @return void
     */
    public function test_valid_registration()
    {

        /* require */
        $request = $this->post('/v1/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'secret',
        ]);

        $request->response->assertJson([
            'success' => 'Account created successfully!'
        ])->assertStatus(200);

    }


}
