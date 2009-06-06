<?php
App::import('Vendor', 'UrlCache.url_cache_app_helper');
App::import('Helper', 'Html');

class UrlCacheTestCase extends CakeTestCase {
  var $HtmlHelper = null;

	function startTest() {
		$this->HtmlHelper = new HtmlHelper();
		$this->HtmlHelper->beforeRender();
	}
	
	function endTest() {
		Cache::delete($this->HtmlHelper->_key, '_cake_core_');
	}

  function testInstances() {
    $this->assertTrue(is_a($this->HtmlHelper, 'HtmlHelper'));
  }
	
	function testUrlRelative() {
		$url = $this->HtmlHelper->url(array('controller' => 'posts'));
		$this->assertEqual($url, '/posts/');
		$this->assertEqual(array('c0662a9d1c026334679f7a02e6c0f8e0' => '/posts/'), $this->HtmlHelper->_cache);
		
		$this->HtmlHelper->afterLayout();
		$cache = Cache::read($this->HtmlHelper->_key, '_cake_core_');
		$this->assertEqual(array('c0662a9d1c026334679f7a02e6c0f8e0' => '/posts/'), $cache);
	}
	
	function testUrlFull() {
		$url = $this->HtmlHelper->url(array('controller' => 'posts'), true);
		$this->assertPattern('/http:\/\/(.*)\/posts/', $url);
		$this->assertEqual(array('35bc4241bbf31c0d0fe6b12bc9c0bce0'), array_keys($this->HtmlHelper->_cache));
		$this->assertPattern('/http:\/\/(.*)\/posts/', $this->HtmlHelper->_cache['35bc4241bbf31c0d0fe6b12bc9c0bce0']);

		$this->HtmlHelper->afterLayout();
		$cache = Cache::read($this->HtmlHelper->_key, '_cake_core_');
		$this->assertEqual(array('35bc4241bbf31c0d0fe6b12bc9c0bce0'), array_keys($cache));
		$this->assertPattern('/http:\/\/(.*)\/posts/', $cache['35bc4241bbf31c0d0fe6b12bc9c0bce0']);
	}
	
	function testUrlRelativeAndFull() {
		$this->HtmlHelper->url(array('controller' => 'posts'));
		$this->HtmlHelper->url(array('controller' => 'posts'), true);
		
		$this->assertEqual(array('c0662a9d1c026334679f7a02e6c0f8e0', '35bc4241bbf31c0d0fe6b12bc9c0bce0'), array_keys($this->HtmlHelper->_cache));

		$this->HtmlHelper->afterLayout();
		$cache = Cache::read($this->HtmlHelper->_key, '_cake_core_');
		$this->assertEqual(array('c0662a9d1c026334679f7a02e6c0f8e0', '35bc4241bbf31c0d0fe6b12bc9c0bce0'), array_keys($cache));
	}
}