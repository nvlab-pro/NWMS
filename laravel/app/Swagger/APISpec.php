<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.1.0",
 *     info=@OA\Info(
 *         version="1.0.0",
 *         title="NWMS API",
 *         description="This API uses Bearer Token authentication. To authorize, use the 'Authorize' button above and enter a token in the format: Bearer {your_token}"
 *     ),
 *     externalDocs=@OA\ExternalDocumentation(
 *         description="The authorization principle is described in detail here",
 *         url="https://nwms.cloud/docs/api"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class APISpec {}
