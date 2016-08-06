<?php

require 'vendor/autoload.php';
require 'src/HtmlBuilder.php';
require 'src/DateUtil.php';
require 'src/CssRenderer.php';
require 'src/JsRenderer.php';

use DateRange\DateRange;
use PhpGantt\HtmlBuilder;
use PhpGantt\DateUtil;
use PhpGantt\CssRenderer;
use PhpGantt\JsRenderer;
use PhpGantt\PhpGantt;

class HtmlBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuildTableHeader() {
    $nonBusinessdays = array(
      strtotime('2016-07-04'),
    );
    $dateUtil = new DateUtil($nonBusinessdays, array(0, 6));
    $htmlBuilder = new HtmlBuilder($dateUtil);

    $tasks = array(
      array(
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'asignee' => 'someone 1',
        'startDate' => strtotime('2016-07-01'),
        'workload' => 3
      ),
      array(
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'asignee' => 'someone 2',
        'dependency' => 'afterPrevious',
        'workload' => 3
      )
    );

    $phpgantt = new PhpGantt($tasks, $nonBusinessdays);
    $tasks = $phpgantt->resolveDependency($tasks);

    $html = $htmlBuilder->buildTableHeader($phpgantt->dates, $tasks);
    $actual = substr_count($html, 'project_name');
    $expected = 5;

    $this->assertSame($actual, $expected);
  }
}
