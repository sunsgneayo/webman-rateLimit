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

        $config         = config('plugin.sunsgne.rate-limit.app.default');
        $capacity       = $config['capacity'] ?? 60;
        $seconds        = $config['seconds'] ?? 60;
        $cost           = $config['cost'] ?? 1;
        $customerHandle = $config['customer'];

        if ($throttler->build($request->getRemoteIp(), $capacity, $seconds, $cost) === false) {
            $newClass = $customerHandle['class'];
            return new $newClass(... \array_values($customerHandle['constructor']));
        }

        return $handler($request);
    }
}