<?php

namespace App\Http\Controllers;

use app\Libraries\Core;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * @OA\Info(
     *   title="JoyBiz API",
     *   version="1.0",
     *   description="This is an API for Register & Login Member",
     *   @OA\Contact(
     *     email="hilal.lucky@gmail.com",
     *     name="Developer"
     *   )
     * )
     */

    public $core;

    public function __construct()
    {
        // Define Core as a global Library
        $this->core = new Core();
    }

    public function missingMethod()
    {
        return $this->core->setResponse();
    }
}
