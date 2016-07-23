<?php

require 'vendor/autoload.php';
require 'src/HtmlBuilder.php';
require 'src/DateUtil.php';

use DateRange\DateRange;
use PhpGantt\HtmlBuilder;
use PhpGantt\DateUtil;

class HtmlBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuildTableHeader() {
    $nonBusinessdays = array(
      strtotime('2016-07-04'),
    );
    $dateUtil = new DateUtil($nonBusinessdays);
    $htmlBuilder = new HtmlBuilder($dateUtil);

    $dates = array(
      strtotime('2016-07-01'),
      strtotime('2016-07-02'),
      strtotime('2016-07-03'),
    );

    $html = $htmlBuilder->buildTableHeader($dates);
    $actual = substr_count($html, 'cell_header');
    $expected = 9;

    $this->assertSame($actual, $expected);
  }
}
