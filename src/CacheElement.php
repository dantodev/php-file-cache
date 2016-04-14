<?php namespace Dtkahl\FileCache;

class CacheElement
{

  private $_cache_instance;
  private $_key;
  private $_value;
  private $_lifetime;
  private $_start;
  private $_refresh;
  private $_modified;

  /**
   * CacheElement constructor.
   * @param $cache_instance
   * @param $key
   * @param $value
   * @param int $lifetime
   * @param int|null $start
   * @param bool $refresh
   */
  public function __construct(Cache $cache_instance, $key, $value, $lifetime = 60, $start = null, $refresh = false)
  {
    $this->_cache_instance = $cache_instance;
    $this->_key = (string) $key;
    $this->_value = (string) $value;
    $this->_lifetime = $lifetime;
    $this->_start = $start ?: time();
    $this->_refresh = $refresh;
    $this->_modified = $start === null;
  }

  /**
   * @param $value
   * @param int $lifetime
   * @param bool $refresh
   */
  public function update($value, $lifetime = 60, $refresh = false)
  {
    $this->_value = (string) $value;
    $this->_lifetime = $lifetime;
    $this->_start = time();
    $this->_refresh = $refresh;
    $this->_modified = true;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    if ($this->_refresh) {
      $this->_start = time();
    }
    return $this->_value;
  }

  /**
   *
   */
  public function writeToFs()
  {
    if ($this->_modified) {
     file_put_contents($this->path(), json_encode([
       "value" => $this->_value,
       "lifetime" => $this->_lifetime,
       "start" => $this->_start,
       "refresh" => $this->_refresh,
     ]));
    }
    $this->_modified = false;
  }

  /**
   * @return bool
   */
  public function removeFromFs()
  {
    return unlink($this->path());
  }

  /**
   * @return bool
   */
  public function isAlive()
  {
    return (time() - $this->_start) <= $this->_lifetime;
  }

  /**
   * @return string
   */
  private function path()
  {
    return $this->_cache_instance->getPathForKey($this->_key);
  }

}