<?php

/**
 * Cache Memcache operate common class
 *
 * @package cache
 * @subpackage cache memcache
 *
 */
class MZ_Memcache {

    /**
     * 保存Memcache主机列表二维数组
     * @var array
     */
    var $hosts = array();

    /**
     * 构造函数
     *
     * @param array $hosts Memcache 主机列表数组，二维数组，例：
     *          array(
     *              array('192.168.0.1', 11211), 
     *              array('192.168.0.2', 11211), 
     *              array('192.168.0.3', 11211),
     *          )
     *  如果不写设定端口号，则默认为 11211
     */
    function __construct($hosts) {
        $this->hosts = $hosts;
    }

    /**
     * Get memcache object
     *
     * @return object
     */
    function getMemcacheObj($hosts = array()) {
        static $memObj;
        if(!$memObj){
            if (!is_array($this->hosts) || empty($this->hosts)){
                return null;
            }
            $memcache = new Memcache();
            foreach($this->hosts as $host){
                if(isset($host[1])){
                    $memcache->addServer($host[0], $host[1]);
                } else {
                    $memcache->addServer($host[0], MEMSERVER_DEFAULT_PORT);
                }
            } 
            $memcache->setCompressThreshold(10000, 0.2);
            $memObj = $memcache;
        }
        return $memObj;
    }

    /**
     * Set variable to memcache 
     * 
     * @param $key
     * @param $value
     * @param $flag
     * @param $expire
     * @return bool
     */
    function set($key, $value, $expire = 0) {
        if(empty($key)) {
            return false;
        }
        $memObj = self::getMemcacheObj();
        return $memObj->set($key, $value, false, $expire);

    }

    /**
     * Fetch variable from memcache
     *
     * @param $key
     * @return false or null
     */
    function get($key) {
        $memObj = self::getMemcacheObj();
        return $memObj->get($key);
    }

    /**
     * Replace variable by memcache
     *
     * @param $key
     * @param $value
     * @return bool
     */
    function replace($key, $value, $expire = 0) {
        $memObj = self::getMemcacheObj();
        return $memObj->replace($key, $value, false, $expire);
    }

    /**
     * Delete variable from memcache
     *
     * @brief
     * @param $key
     * @return bool
     */
    function remove($key) {
        $memObj = self::getMemcacheObj();
        return $memObj->delete($key);
    }

}

