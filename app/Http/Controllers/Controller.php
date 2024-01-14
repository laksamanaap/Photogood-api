<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Photogood Open API Docs",
     *      description="Photogood Open API",
     *      @OA\Contact(
     *          email="laksamana.arya1412@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Photogood API Server"
     * )

     *
     
     */


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
