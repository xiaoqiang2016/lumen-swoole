<?php
namespace App\Http\Controllers;

use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\PathItem;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Server;

/**
 *
 * @Info(
 *     version="1.0.0",
 *     title="Sino Click",
 *     description="Sino Click 后端API。",
 * )
 * @Host "local-sinoapi.meetsocial.cn"
 * @PathItem(
 *  path="test"
 * )
 * @Server(
 *     url="http://127.0.0.1:9501",
 *     description="本地环境",
 * )
 *
 * @Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     description="响应实体，响应结果统一使用该结构",
 *     title="响应实体",
 *     @Property(
 *         property="code",
 *         type="integer",
 *         description="响应代码"
 *     ),
 *     @Property(
 *         property="message",
 *         type="string",
 *         description="响应结果提示"
 *     ),
 *     @Property(
 *         property="result",
 *         type="string",
 *         description="响应结果"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="access_token",
 *     name="access_token"
 * )
 *
 * @package App\Http\Controllers
 */
class SwaggerController
{}