<?php

namespace sunsgne\middleware;

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
        $moreRateLimit = Container::get(MoreRateLimit::class);

        $config         = config('plugin.sunsgne.rate-limit.app.default');
        $capacity       = $config['capacity'] ?? 60;
        $seconds        = $config['seconds'] ?? 60;
        $customerHandle = $config['customer'];


        if (false === $moreRateLimit->handle(intval($capacity), intval($seconds))) {
            $newClass = $customerHandle['class'];
            return new $newClass(... \array_values($customerHandle['constructor']));
        }
        return $handler($request);

    }

}