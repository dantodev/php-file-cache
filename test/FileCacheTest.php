<?php namespace Dtkahl\FileCache;

class FileCacheTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $cache = new Cache(__DIR__ . "/test_path/");

        // get if not set
        $this->assertNull($cache->get("foo"));
        $this->assertEquals("default", $cache->get("foo", "default"));

        // test has = false
        $this->assertFalse($cache->has("foo"));

        // set & get if set
        $cache->set("foo", "bar");
        $this->assertEquals("bar", $cache->get("foo"));

        // test has = true
        $this->assertTrue($cache->has("foo"));

        //test if cache has been written
        $cache->writeCache()->flush(true);
        $this->assertEquals("bar", $cache->get("foo"));

        // test remove
        $cache->forget("foo");
        $this->assertNull($cache->get("foo"));

        // test remember
        $this->assertEquals("bar2", $cache->remember("foo2", function () {
            return "bar2";
        }));
        $this->assertEquals("bar2", $cache->remember("foo2", function () {
            return "this will never be set";
        }));

        // test timeout
        $cache->set("foo3", "bar3", 1);
        sleep(2);
        $this->assertNull($cache->get("foo3"));

        // test refresh
        $cache->set("foo4", "bar4", 4);
        sleep(2);
        $this->assertEquals("bar4", $cache->get("foo4"));
        sleep(2);
        $this->assertEquals("bar4", $cache->get("foo4"));
        sleep(5);
        $this->assertNull($cache->get("foo4"));

        // test flush
        $cache->flush();
    }

}