<?php

namespace sunsgne\middleware;

use support\Container;
use sunsgne\RateLimitHandler;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * @purpose 适用于单进程无并发的限流中间件
 * @date 2022/5/18
 * @author zhulianyou
 */
class RateLimit implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        $throttler = Container::get(RateLimitHandler::class);

        $capacity       =  60;
        $seconds        =  60;
        $cost           =  1;
        $customerHandle = [
            'class'       => \support\Response::class,
            'constructor' => [
                429,
                array(),
                json_encode(['success' => false, 'msg' => '请求次数太频繁'], 256),
            ],
        ];

        if ($throttler->handle($request->getRemoteIp(), $capacity, $seconds, $cost) === false) {
            $newClass = $customerHandle['class'];
            return new $newClass(... \array_values($customerHandle['constructor']));
        }

        return $handler($request);
    }
}