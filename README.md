# webman 服务限流

> 基于illuminate/redis 的令牌桶限流中间件

## 安装

```sh
composer require sunsgne/rate-limit
```

## 使用
> 在 `config/plugin/sunsgne/rate-limit/app.php`中配置
```php
    'default' => [
        /** The number of requests the "bucket" can hold || “桶”可以容纳的请求数 */
        'capacity'    => 100,
        /** The time it takes the "bucket" to completely refill || “桶”完全重新装满所需的时间  */
        'seconds'     => 60,
        'cost'        => 1, //使用的令牌数 
        'concurrency' => true, //开启并发限流
        'customer'    => [
            'class'       => \support\Response::class, //需要进行限流的处理的回调类
            'constructor' => [
                429, // HTTP状态码
                array(), // response header参数
                json_encode(['success' => false, 'msg' => '请求次数太频繁'], 256), // response Body参数
            ],
        ],
    ],
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

> 进程数`24`,`concurrency=>ture`,无业务逻辑


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 13      | 5       | 4       | 215     | 101.1  |
| 1001   | 241     | 244     | 49      | 428     | 952.4  |
| 10001  | 1600    | 2140    | 0       | 3249    | 1643.0 |


> 进程数`24`,`concurrency=>fasle`,无业务逻辑


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 7.0     | 7       | 6       | 11      | 104.1  |
| 1001   | 18      | 15      | 4.0     | 123     | 983.4  |
| 10001  | 1820    | 2359    | 0       | 3240    | 1793.0 |

#### 数据库查询

> 进程数`24`,`concurrency=>ture`


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 351     | 302     | 4       | 1019    | 80.1   |



> 进程数`24`,`concurrency=>fasle`


| 并发数    | 平均值(ms) | 中位数(ms) | 最小值(ms) | 最大值(ms) | 吞吐量    |
|--------|---------|---------|---------|---------|--------|
| 101    | 224     | 51      | 10      | 1004    | 70.1   |





