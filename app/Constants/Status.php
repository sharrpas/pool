<?php

namespace App\Constants;

class Status
{
    public const OPERATION_SUCCESSFUL = 200;
    public const VALIDATION_FAILED = 400;
    public const AUTHENTICATION_FAILED = 401;
    public const TOO_MANY_ATTEMPTS = 402;
    public const PERMISSION_DENIED = 403;
    public const NOT_FOUND = 404;
    public const ROUTE_NOT_FOUND = 410;
    public const OPERATION_ERROR = 444;
    public const Unexpected_Problem = 500;

    public static function getMessage($code)
    {
        $messages = [
            self::OPERATION_SUCCESSFUL => "عملیات با موفقیت انجام شد",
            self::OPERATION_ERROR => "عملیات با شکست مواجه شد",
            self::VALIDATION_FAILED => "اعتبار سنجی ناموفق بود",
            self::AUTHENTICATION_FAILED => "احراز هویت ناموفق بود",
            self::TOO_MANY_ATTEMPTS => "درخواست های خیلی زیاد",
            self::PERMISSION_DENIED => "اجازه رد شد",
            self::NOT_FOUND => "پیدا نشد",
            self::ROUTE_NOT_FOUND => "مسیر انتخاب شده نامعتبر است",
            self::Unexpected_Problem => "یک مشکل غیرمنتظره رخ داد. لطفا دوباره تلاش کنید",
        ];

        return $messages[$code];
    }
}
