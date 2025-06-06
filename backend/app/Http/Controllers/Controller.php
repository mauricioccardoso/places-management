<?php

namespace App\Http\Controllers;

/**
 *
 * @OA\Info( version="1.0", title="Gerenciamento de Lugares")
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Local"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   description="API access via JWT",
 *   in="header",
 *   name="Authorization"
 * )
 *
 * @OA\OpenApi(
 *   security={
 *     {"bearerAuth": {}}
 *   }
 * )
 *
 */
abstract class Controller
{
    //
}
