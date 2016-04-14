<?php namespace Dtkahl\FileCache;

class FileCache
{

  private $_path;
  private $_default_lifetime;
  private $_default_refresh;

  // TODO single/multi file ?
  // TODO instant write ?

  public function __construct($path, $default_lifetime = 60, $default_refresh = false)
  {
    $this->_path = $path;
    $this->_default_lifetime = $default_lifetime;
    $this->_default_refresh = $default_refresh;
    $this->_cache = [];
    $this->_modified_keys = [];
    $this->_removed_keys = [];
  }

  public function has($key)
  {
    /*
     * hash key
     * test in cache array
     * test in filesystem
     */
  }

  public function get($key, $default_value)
  {
    /*
     * hash key
     * get from cache array
     * get from filesystem
     */
  }

  static function set($key, $value, $lifetime = null, $refresh = null)
  {
    /*
     * hash key
     * set to cache array
     * set modified_keys
     */
  }

  public function forget($key)
  {
    /*
     * hash key
     * remove from cache array
     * set removed_keys
     */
  }

  public function remember($key, \Closure $call, $lifetime = null, $refresh = null)
  {
    /*
     * has key ?
     *  - true : get key
     *  - false: set key , call $call
     */
  }

  public function writeCache()
  {
    /*
     * write modified_keys to fs
     * remove removed_keys from fs
     */
  }

}