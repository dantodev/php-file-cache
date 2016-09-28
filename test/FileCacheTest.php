<?php namespace Dtkahl\FileCache;

class FileCacheTest extends \PHPUnit_Framework_TestCase
{

    /** @var Cache */
    private $cache;
    
    public function setUp()
    {
        $this->cache = new Cache(__DIR__ . "/test_path/");
        parent::setUp();
    }
    
    public function testGetHasEmpty()
    {
        $this->assertNull($this->cache->get("foo"));
        $this->assertEquals("default", $this->cache->get("foo", "default"));
        $this->assertFalse($this->cache->has("foo"));
    }

    public function testGetHasExisting()
    {
        $this->cache->set("foo", "bar");
        $this->assertEquals("bar", $this->cache->get("foo"));
        $this->assertTrue($this->cache->has("foo"));

        //test if cache has been written
        $this->cache->writeCache()->flush(true);
        $this->assertEquals("bar", $this->cache->get("foo"));
    }

    public function testRemove()
    {
        $this->cache->set("foo", "bar");
        $this->assertEquals("bar", $this->cache->get("foo"));
        $this->cache->forget("foo");
        $this->assertNull($this->cache->get("foo"));
    }

    public function testRemember()
    {
        $this->assertEquals("bar2", $this->cache->remember("foo2", function () {
            return "bar2";
        }));
        $this->assertEquals("bar2", $this->cache->remember("foo2", function () {
            return "this will never be set";
        }));
    }

    public function testTimeout()
    {
        // test timeout
        $this->cache->set("foo3", "bar3", 1);
        sleep(2);
        $this->assertNull($this->cache->get("foo3"));

        // test refresh
        $this->cache->set("foo4", "bar4", 4);
        sleep(2);
        $this->assertEquals("bar4", $this->cache->get("foo4"));
        sleep(2);
        $this->assertEquals("bar4", $this->cache->get("foo4"));
        sleep(5);
        $this->assertNull($this->cache->get("foo4"));
    }

    public function testSerialization()
    {
        $this->cache->set('foo4', ['item1', 'item2']);
        $this->assertEquals(['item1', 'item2'], $this->cache->get('foo4'));
    }

}