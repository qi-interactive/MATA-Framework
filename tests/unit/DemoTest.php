<?php
namespace yiiunit;
use Yii;

/**
 * This is the base class for all yii framework unit tests, which requires
 * external vendor libraries to function.
 */
class VendorTestCase extends \PHPUnit_Framework_TestCase {

	public function testCheck() { 
		$this->assertEquals(4, 2+2);
	}
}
