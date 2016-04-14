<?php namespace Dtkahl\FileCache;

class Cache
{

  private $_path;
  private $_default_lifetime;
  private $_default_refresh;

  /** @var CacheElement[] */
  private $_cache = [];

  public function __construct($path, $default_lifetime = 60, $default_refresh = false)
  {
    $this->_path = $path;
    $this->_default_lifetime = $default_lifetime;
    $this->_default_refresh = $default_refresh;
  }

  public function has($key)
  {
    if (!is_null($element = $this->getOrFetchCacheElement($key))) {
      if ($element->isAlive()) {
        return true;
      }else {
        $this->forget($key);
      }
    }
    return false;
  }

  public function get($key, $default_value = null)
  {
    if (!is_null($element = $this->getOrFetchCacheElement($key))) {
      if ($element->isAlive()) {
        return $element->getValue();
      }else {
        $this->forget($key);
      }
    }
    return $default_value;
  }

  public function set($key, $value, $lifetime = null, $refresh = null)
  {
    if (is_null($element = $this->getOrFetchCacheElement($key))) {
      $this->_cache[$key] = $this->newCacheElement($key, $value, $lifetime, $refresh);
    } else {
      $element->update(
          $value,
          $lifetime ?: $this->_default_lifetime,
          $refresh ?: $this->_default_refresh
      );
    }
    return $this;
  }

  public function forget($key)
  {
    if (!is_null($element = $this->getOrFetchCacheElement($key))) {
      $element->removeFromFs();
      unset($this->_cache[$key]);
    }
    return $this;
  }

  public function remember($key, \Closure $call, $lifetime = null, $refresh = null)
  {
    if (!$this->has($key)) {
      $this->_cache[$key] = $this->newCacheElement($key, $call(), $lifetime, $refresh);
    }
    return $this;
  }

  public function flush()
  {
    array_map("unlink", glob($this->_path . "/cache_*.json"));
  }

  public function writeCache()
  {
    foreach ($this->_cache as $element) {
      $element->writeToFs();
    }
  }

  public function getPathForKey($key)
  {
    return $this->_path . "/cache_" . md5($key) . ".json";
  }

  /**
   * @param $key
   * @return CacheElement|null
   */
  private function getOrFetchCacheElement($key)
  {
    $element = $this->getCacheElement($key);
    if (is_null($element) && !is_null($element = $this->fetchCacheElement($key))) {
      $this->_cache[$key] = $element;
    }
    return $element;
  }

  /**
   * @param $key
   * @return CacheElement|null
   */
  private function getCacheElement($key)
  {
    return array_key_exists($key, $this->_cache) ? $this->_cache[$key] : null;
  }

  /**
   * @param $key
   * @return CacheElement|null
   */
  private function fetchCacheElement($key)
  {
    $path = $this->getPathForKey($key);
    if (is_file($path)) {
      $cache = json_decode(file_get_contents($path));
      $element =  new CacheElement(
          $this,
          $key,
          $cache["value"],
          $cache["lifetime"],
          $cache["start"],
          $cache["refresh"]
      );
      $this->_cache[$key] = $element;
      return $element;
    }
    return null;
  }

  /**
   * @param $key
   * @param $value
   * @param int|null $lifetime
   * @param int|null $refresh
   * @return CacheElement
   */
  private function newCacheElement($key, $value, $lifetime = null, $refresh = null)
  {
    return new CacheElement(
        $this,
        $key,
        $value,
        $lifetime ?: $this->_default_lifetime,
        null,
        $refresh ?: $this->_default_refresh
    );
  }

}