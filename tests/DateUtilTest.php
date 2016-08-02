<?php

require 'vendor/autoload.php';

use PhpGantt\DateUtil;

class DateUtilTest extends PHPUnit_Framework_TestCase {
  public function testIsBusinessday() {
    $nonBusinessdays = array(
      strtotime('2016-07-04'),
    );
    $dateUtil = new DateUtil($nonBusinessdays, array(0, 6));
    $result = $dateUtil->isBusinessday(strtotime('2016-07-04'));
    $this->assertFalse($result);
  }
}
