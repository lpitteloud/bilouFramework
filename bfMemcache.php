<?php #coding: utf-8
/**
 * @file bfMemcache.php
 */

class bfMemcache extends bfApi
{
    const MEMCACHE_HOST = 'localhost';
    const MEMCACHE_PORT = 11211;
    const MEMCACHE_CONNECT_TIMEOUT = 2;

    protected $memcache;
	protected $cacheDefaultTimeout;
	protected $cache;

    public function __construct()
    {
        $this->memcache = null; // Not null means cache system is used.
        $this->cacheDefaultTimeout = 0; // cache never expire.
        $this->cache = array();
    }

    ###################################################
    ## cacheEnabled() #

    //bool
    static public function cacheEnabled()
    {
        if (is_defined(CACHE_ENABLED) && bf::is_int(CACHE_ENABLED))
            return CACHE_ENALBED;
        
        return false;
    }

    ####################################################
    ## connect() #

    # E_OK
    # E_SYS
    protected function connect()
    {
        if($this->memcache === null)
        {
            $this->memcache = new Memcache;
            if(!@$this->memcache->connect(self::MEMCACHE_HOST, self::MEMCACHE_PORT, self::MEMCACHE_CONNECT_TIMEOUT))
            {
                $this->memcache = -1;
                return self::sys();
            }
            return self::ok();
        }
		elseif(is_int($this->memcache) && ($this->memcache === -1))
			return self::sys();

        return self::ok();
    }

    #######################################################
    ## getCache() #

    # E_OK
    # E_SYS
    # E_NOSUCH
    public function getCache($cacheName, &$output)
    {
        if(self::cacheEnabled() === false)
            return self::nosuch();

		if(array_key_exists($cacheName, $this->cache) && $this->cache[$cacheName] !== null)
        {
            yErrorLogger::logError('Duplicate memcache call for key: '.$cacheName);
            yErrorLogger::saveLog();
			return $this->cache[$cacheName];
        }

        if(($ret = $this->connect()) !== self::E_OK)
            return $ret;

		if(($res = $this->memcache->get($cacheName)) !== false)
		{
            $this->cache[$cacheName] = $output = $res;
			return self::ok();
		}
		else
			return self::nosuch();
    }

    #########################################################
    ## setCache() #

    # E_OK
    # E_SYS
    public function setCache($key, $input, $flag=0, $timeout=0)
    {
        if(($ret = $this->connect()) !== self::E_OK)
            return $ret;

        if(($ret = $this->memcache->set($key, $input, $flag, $timeout)) !== false)
            return self::ok();
        else
            return self::sys();
    }

    #########################################################
    ## setDefaultTimeout() #

    // void
    public function setDefaultTimeout($timeout)
    {
        $this->cacheDefaultTimeout = is_int($timeout) ? $timeout : $this->cacheTimeout;
    }
}
?>
