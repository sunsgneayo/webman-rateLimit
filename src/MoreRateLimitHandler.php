<?php

namespace sunsgne;

use support\Redis;

/**
 * @purpose 并发时的限流处理
 * @date 2022/7/6
 * @author zhulianyou
 */
class MoreRateLimitHandler
{
    public const LIMIT_TRAFFIC_SCRIPT_SHA = 'rateLimit:script';

    public const LIMIT_TRAFFIC_PRE = 'rateLimit:pre:';

    /**
     * @param int $capacity
     * @param int $seconds
     * @return bool
     * @datetime 2022/7/6 10:22
     * @author zhulianyou
     */
    public static function handle(int $capacity , int $seconds): bool
    {
        $scriptSha = Redis::get(self::LIMIT_TRAFFIC_SCRIPT_SHA);
        if (!$scriptSha) {
            $script = <<<luascript
            local result = redis.call('SETNX', KEYS[1], 1);
            if result == 1 then
                return redis.call('expire', KEYS[1], ARGV[2])
            else
                if tonumber(redis.call("GET", KEYS[1])) >= tonumber(ARGV[1]) then
                    return 0
                else
                    return redis.call("INCR", KEYS[1])
                end
            end
luascript;
            $scriptSha = Redis::script('load', $script);
            Redis::set(self::LIMIT_TRAFFIC_SCRIPT_SHA, $scriptSha);
        }
        $limitKey = self::LIMIT_TRAFFIC_PRE . request()->getRealIp();
        $result = Redis::rawCommand('evalsha', $scriptSha, 1, $limitKey, $capacity, $seconds);
        return (bool)$result;

    }

}