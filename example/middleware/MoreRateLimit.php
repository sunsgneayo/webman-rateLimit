<?php

namespace sunsgne\middleware;

use sunsgne\MoreRateLimitHandler;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * @purpose 并发时限流中间件
 * @date 2022/7/6
 * @author zhulianyou
 */
class MoreRateLimit implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        $moreRateLimit = Container::get(MoreRateLimitHandler::class);

        $capacity       =  60;
        $seconds        =  60;
        $customerHandle = [
            'class'       => \support\Response::class,
            'constructor' => [
                429,
                array(),
                json_encode(['success' => false, 'msg' => '请求次数太频繁'], 256),
            ],
        ];


        if (false === $moreRateLimit->handle(intval($capacity), intval($seconds))) {
            $newClass = $customerHandle['class'];
            return new $newClass(... \array_values($customerHandle['constructor']));
        }
        return $handler($request);

    }

}