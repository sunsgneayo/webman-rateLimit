# webman 接口限流

基于illuminate/redis 的令牌桶限流中间件

## 安装

```sh
composer require sunsgne/rate-limit
```

## 使用
```php
'enable' => true,
'capacity' => 60, // “桶”可以容纳的请求数
'seconds' => 60,  // “桶”完全重新装满所需的时间
'cost' => 1,      // 此操作使用的令牌数
```
### 参数示例
| 参数名  | 示例  |  说明 |
| -------- |-----| ----- |
| capacity | 60  | “桶”可以容纳的请求数 |
| seconds | 60  | “桶”完全重新装满所需的时间 |
| cost | 1   | “桶”此操作使用的令牌数 |


