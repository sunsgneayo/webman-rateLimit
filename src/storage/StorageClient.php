<?php

namespace sunsgne\storage;

use SQLite3Result;
use WorkBunny\Storage\Driver;

/**
 * @purpose
 * @date 2022/9/19
 * @author zhulianyou
 */
class StorageClient
{

    /** @var Driver sqlite驱动 */
    protected static Driver $client;


    protected static string $dbFileName = 'rate-limit';


    public function __construct()
    {
        if (! (self::$client instanceof  Driver) )
        {
            self::$client = new Driver([
                'filename'      => self::$dbFileName,
                'flags'         => 'SQLITE3_OPEN_READWRITE|SQLITE3_OPEN_CREATE',
                'encryptionKey' => ''
            ]);
        }
    }
    public function initDB()
    {
        self::$client->create(self::$dbFileName, [
            'ip' => [
                'INT',
                'PRIMARY KEY',
                'NOT NULL',
            ],
            'request' => [
                'INT(5)',
                'NOT NULL',
            ],
            'created_at' => [
                'timestamp'
            ],
            'updated_at' => [
                'timestamp'
            ],

        ],[
            'CREATE INDEX `rate-limit-ip` ON `rate-limit` (`ip`);'
        ]);
    }


    public function handle(string $clientIp ,int $capacity , int $seconds)
    {
        $nowTime = time();

        $res = self::$client->query('SELECT `ip` , `request` , `updated_at` FROM `rate-limit` WHERE `ip` = '.$clientIp.';');

        if($res instanceof SQLite3Result){
            var_dump($res->fetchArray());
            $ipResult = $res->fetchArray();
        }


        /** 存在此前IP的请求 */
        if (isset($ipResult) and !empty($ipResult))
        {
            if (($ipResult["updated_at"] + $seconds) < $nowTime)
            {
                /** 不在限流时间内 ,重置限流请求次数，并正常返回  */
                self::$client->query('UPDATE `rate-limit` SET `request` = 1 , `updated_at` = '. $nowTime.' WHERE `ip` = '.$clientIp .';');
                return intval($capacity - 1);
            }

            if ( ($ipResult["request"] ++)  < $capacity)
            {
                /** 正常返回且更新请求次数 */
                self::$client->query('UPDATE `rate-limit` SET `request` = '.$ipResult["request"] ++.'  WHERE `ip` = '.$clientIp .';');
                return  $capacity - ($ipResult["request"] ++);
            }
            /** 限流  */
            return  0;
        }

        /** 当前请求写入库 并正常返回 */

        self::$client->insert('rate-limit', [
            'ip' => $clientIp,
            'request' => 1,
            'created_at' => $nowTime,
            'updated_at' => $nowTime,
        ]);

        return  intval($capacity - 1);

    }

}