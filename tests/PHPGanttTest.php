<?php

require 'src/PHPGantt.php';

class PHPGanttTest extends PHPUnit_Framework_TestCase {
  public function testResolveDependency() {
    $tasks = [
      [
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10
      ],
      [
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'dependency' => 'afterPrevious',
        'workload' => 10
      ]
    ];
    $nonBusinessdays = [
      strtotime('2015-10-02'),
      strtotime('2015-10-06'),
      strtotime('2015-11-23'),
      strtotime('2015-11-24')
    ];
    $gantt = new PHPGantt($tasks, $nonBusinessdays);
    $actual = $gantt->resolveDependency($tasks);
    $expected = [
      [
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10,
        'endDate' => strtotime('2015-10-19')
      ],
      [
        'project' => 'Project 2',
        'name' => 'hello world 3',
        'dependency' => 'afterPrevious',
        'workload' => 10,
        'startDate' => strtotime('2015-10-20'),
        'endDate' => strtotime('2015-11-02')
      ]
    ];
    $this->assertSame(
      $expected,
      $actual
    );
  }

  public function testRemoveNonBusinessDays() {
    $tasks = [
      [
        'project' => 'Project 1',
        'name' => 'hello world',
        'startDate' => strtotime('2015-10-01'),
        'workload' => 5
      ],
      [
        'project' => 'Project 1',
        'name' => 'hello world 2',
        'startDate' => strtotime('2015-10-05'),
        'workload' => 10 
      ],
      [
        'project' => 'Project 2',
        'name' => 'hello world 3',
        // 'startDate' => strtotime('2015-10-10'),
        'dependency' => 'afterPrevious',
        'workload' => 10 
      ]
    ];
    $nonBusinessdays = [
      strtotime('2015-10-02'),
      strtotime('2015-10-06'),
      strtotime('2015-11-23'),
      strtotime('2015-11-24')
    ];
    $gantt = new PHPGantt($tasks, $nonBusinessdays);
    $dates = [
      strtotime('2015-10-01'),
      strtotime('2015-10-02'),
      strtotime('2015-10-03'),
      strtotime('2015-10-04'),
      strtotime('2015-10-05')
    ];
    $actual = $gantt->removeNonBusinessdays($dates);
    $expected = [
      strtotime('2015-10-01'),
      strtotime('2015-10-05'),
      strtotime('2015-10-07'),
      strtotime('2015-10-08'),
      strtotime('2015-10-09')
    ];
    $this->assertSame($expected, $actual);
  }
}
