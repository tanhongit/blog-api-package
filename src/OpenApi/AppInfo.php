<?php

namespace CSlant\Blog\Api\OpenApi;

use OpenApi\Attributes\Server;

/**
 * @OA\Info(
 *     description="The API Documentation for CSlant Blog - Build by cslant.com",
 *     version="1.0.0",
 *     title="The API Documentation",
 *     termsOfService="https://swagger.io/terms/",
 *     @OA\Contact(
 *         email="contact@cslant.com",
 *         name="CSlant",
 *         url="https://cslant.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\Schemes(
 *     scheme="http",
 *     scheme="https",
 * )
 * @OA\ExternalDocumentation(
 *     description="Find out more about Swagger",
 *     url="https://swagger.io"
 * )
 * @OA\PathItem(
 *     path="/",
 * )
 * @OA\Server(
 *     url=YOUR_DYNAMIC_URL_CONSTANT,
 *     description="OpenApi dynamic host local"
 * )
 *
 * @OA\Server(
 *     url="YOUR_DYNAMIC_PROD_URL_CONSTANT",
 *     description="OpenApi dynamic host server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Authentication by bearer token",
 *     name="bearerAuth",
 *     in="header",
 *     scheme="bearer",
 *     securityScheme="sanctum"
 * )
 */
class AppInfo
{
}
