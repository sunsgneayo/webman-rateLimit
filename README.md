<p align="center"><img width="260px" src="https://chaz6chez.cn/images/workbunny-logo.png" alt="workbunny"></p>

**<p align="center">workbunny/rate-limit</p>**

**<p align="center">🐬 A PHP implementation of current limiting middleware for webman service HTTP-API 🐬</p>**

# A PHP implementation of current limiting middleware for webman service HTTP-API

[![Latest Stable Version](http://poser.pugx.org/sunsgne/rate-limit/v)](https://packagist.org/packages/workbunny/webman-nacos)
[![Total Downloads](http://poser.pugx.org/sunsgne/rate-limit/downloads)](https://packagist.org/packages/workbunny/webman-nacos)
[![Latest Unstable Version](http://poser.pugx.org/sunsgne/rate-limit/v/unstable)](https://packagist.org/packages/workbunny/webman-nacos)
[![License](http://poser.pugx.org/sunsgne/rate-limit/license)](https://packagist.org/packages/workbunny/webman-nacos)
[![PHP Version Require](http://poser.pugx.org/sunsgne/rate-limit/require/php)](https://packagist.org/packages/workbunny/webman-nacos)

# webman 服务限流

> 基于illuminate/redis 的令牌桶限流中间件

## 安装

```sh
composer require workbunny/rate-limit
```

## 使用
> 在 `webman`中使用
```php
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
```

> 直接使用
```php
$moreRateLimit = new \sunsgne\middleware\MoreRateLimit();
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
```
### 参数示例
| 参数名  | 示例  |  说明 |
| -------- |-----| ----- |
| capacity | 60  | “桶”可以容纳的请求数 |
| seconds | 60  | “桶”完全重新装满所需的时间 |
| concurrency | true  | 开启并发限流 |
| cost | 1   | “桶”此操作使用的令牌数 |


### 测试说明
#### 不含数据库查询

> 进程数`24`,`moreRateLimit`,无业务逻辑


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 13      | 5       | 4       | 215     | 101.1  |
| 1001   | 241     | 244     | 49      | 428     | 952.4  |
| 10001  | 1600    | 2140    | 0       | 3249    | 1643.0 |


> 进程数`24`,`RateLimit`,无业务逻辑


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 7.0     | 7       | 6       | 11      | 104.1  |
| 1001   | 18      | 15      | 4.0     | 123     | 983.4  |
| 10001  | 1820    | 2359    | 0       | 3240    | 1793.0 |

#### 数据库查询

> 进程数`24`,`moreRateLimit`


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 351     | 302     | 4       | 1019    | 80.1   |



> 进程数`24`,`RateLimit`


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 224     | 51      | 10      | 1004    | 70.1   |


### 更新记录
#### 1.1.3 - 2022-07-06
##### 新增
- redis 使用 `lua` 脚本加锁


