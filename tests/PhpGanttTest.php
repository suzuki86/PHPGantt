<?php

require 'vendor/autoload.php';
require 'src/PhpGantt.php';

use DateRange\DateRange;
use PhpGantt\PhpGantt;

class PhpGanttTest extends PHPUnit_Framework_TestCase {
  public function testResolveDependency() {
    $tasks = array(
      array(
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'asignee' => 'someone 1',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10
      ),
      array(
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'asignee' => 'someone 2',
        'dependency' => 'afterPrevious',
        'workload' => 10
      )
    );
    $nonBusinessdays = array(
      strtotime('2015-10-02'),
      strtotime('2015-10-06'),
      strtotime('2015-11-23'),
      strtotime('2015-11-24')
    );
    $gantt = new PhpGantt($tasks, $nonBusinessdays);
    $actual = $gantt->resolveDependency($tasks);
    $expected = array(
      array(
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'asignee' => 'someone 1',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10,
        'endDate' => strtotime('2015-10-19')
      ),
      array(
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'asignee' => 'someone 2',
        'dependency' => 'afterPrevious',
        'workload' => 10,
        'startDate' => strtotime('2015-10-20'),
        'endDate' => strtotime('2015-11-02')
      )
    );
    $this->assertSame(
      $expected,
      $actual
    );
  }

  public function testRemoveNonBusinessDays() {
    $tasks = array(
      array(
        'project' => 'Project 1',
        'name' => 'hello world',
        'asignee' => 'someone 1',
        'startDate' => strtotime('2015-10-01'),
        'workload' => 5
      ),
      array(
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'asignee' => 'someone 2',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10 
      ),
      array(
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'asignee' => 'someone 3',
        'dependency' => 'afterPrevious',
        'workload' => 10 
      )
    );
    $nonBusinessdays = array(
      strtotime('2015-10-02'),
      strtotime('2015-10-06'),
      strtotime('2015-11-23'),
      strtotime('2015-11-24')
    );
    $gantt = new PhpGantt($tasks, $nonBusinessdays);
    $dates = array(
      strtotime('2015-10-01'),
      strtotime('2015-10-02'),
      strtotime('2015-10-03'),
      strtotime('2015-10-04'),
      strtotime('2015-10-05')
    );
    $actual = $gantt->dateUtil->removeNonBusinessdays($dates);
    $expected = array(
      strtotime('2015-10-01'),
      strtotime('2015-10-05'),
      strtotime('2015-10-07'),
      strtotime('2015-10-08'),
      strtotime('2015-10-09')
    );
    $this->assertSame($expected, $actual);
  }

  public function testFilterTasks() {
    $tasks = array(
      array(
        'project' => 'Project 1',
        'name' => 'hello world',
        'asignee' => 'someone 1',
        'startDate' => strtotime('2015-10-01'),
        'workload' => 5
      ),
      array(
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'asignee' => 'someone 2',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10
      ),
      array(
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'asignee' => 'someone 3',
        'dependency' => 'afterPrevious',
        'workload' => 10
      )
    );
    $nonBusinessdays = array(
      strtotime('2015-10-02'),
    );
    $filters = array(
      'project' => 'Project 2'
    );
    $gantt = new PhpGantt($tasks, $nonBusinessdays);
    $actual = $gantt->filterTasks($tasks, $filters);
    $expected = array(
      array(
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'asignee' => 'someone 3',
        'dependency' => 'afterPrevious',
        'workload' => 10
      )
    );
    $this->assertSame($expected, $actual);
  }
}
