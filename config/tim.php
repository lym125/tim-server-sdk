<?php

/**
 * 腾讯即时通讯 IM 服务端 REST API 调用配置
 *
 * https://cloud.tencent.com/document/product/269/1519
 *
 */
return [
    'im' => [
        'default' => [
            'sdk_app_id' => env('TIM_SDK_APP_ID', ''),
            'secret_key' => env('TIM_SECRET_KEY', ''),
            'identifier' => env('TIM_IDENTIFIER', ''),
        ],
    ],
];
