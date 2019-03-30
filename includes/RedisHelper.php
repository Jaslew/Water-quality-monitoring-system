<?php

class RedisHelper{
    
    private $redis;
    private $host = '127.0.0.1';
    private $port = '6379';
    
    public function __construct(){
        $this->redis = new Redis();
        $this->redis->connect($this->host, $this->port);
    }

    //哈希表
    public function hset($key, $field, $val){
        return $this->redis->hSet($key, $field, $val);
    }
    
    public function hget($key, $field){
        return $this->redis->hGet($key, $field);
    }

    public function hdel($key, $field){
        return $this->redis->hdel($key, $field);
    }
    
    public function hexists($key, $field){
        return $this->redis->hExists($key, $field);
    }
    
    public function hkeys($key){
        return $this->redis->hKeys($key);
    }
    
    public function hvals($key){
        return $this->redis->hVals($key);
    }
    
    public function del($key){
        return $this->redis->del($key);
    }
    
}