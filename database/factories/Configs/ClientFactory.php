<?php

namespace Database\Factories\Configs;

use App\Models\Configs\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'ip' => '127.0.0.1',
            'domain_name' => $this->faker->domainName(),
            'client_key' => $this->faker->asciify('********************'),
            'status' => 1,
            'description' => 'localhost testing',
        ];
    }
}
