<?php

namespace PhpGantt;

use DateRange\DateRange;

class PhpGantt {

  /**
   * Array of tasks.
   */
  public $tasks;

  /**
   * First date of whole date range.
   */
  public $startDate;

  /**
   * Last date of whole date range.
   */
  public $endDate;

  /**
   * HTML to output.
   */
  public $contents;

  /**
   * All dates of gantt chart.
   */
  public $dates = array();

  /**
   * Date range of gantt chart.
   */
  public $dateRange;

  /**
   * Instance of DateUtil.
   */
  public $dateUtil;

  /**
   * Filter
   */
  public $filters = array();

  public function __construct($tasks, $nonBusinessdays, $filters = array(), $dayOff = array(0, 6)) {
    $this->filters = $filters;
    $this->nonBusinessdays = $nonBusinessdays;

    $this->dateUtil = new DateUtil($nonBusinessdays, $dayOff);
    $this->htmlBuilder = new HtmlBuilder($this->dateUtil);

    $this->tasks = $this->resolveDependency($tasks);
    $this->tasks = $this->filterTasks($this->tasks, $this->filters);
    $this->extractDates($this->tasks);
    $this->dateRange = new DateRange(min($this->dates), max($this->dates));
    $this->dates = $this->dateRange->extract();
    $this->contents = $this->htmlBuilder->build($this->dates, $this->tasks);
  }

  public function addTasks($tasks) {
    $this->tasks[] = $tasks;
  }

  public function filterTasks($tasks, $filters) {
    foreach($tasks as $task) {
      if (
          (
            isset($filters['project'])
            && $task['project'] !== $filters['project']
          ) || (
            isset($filters['assignee'])
            && $task['assignee'] !== $filters['assignee']
          )
      ) {
        continue;
      }
      $results[] = $task;
    }
    return $results;
  }

  public function hasDuplicate($array1, $array2) {
    foreach ($array1 as $element) {
      if (in_array($element, $array2)) {
        return true;
      }
    }
    foreach ($array2 as $element) {
      if (in_array($element, $array1)) {
        return true;
      }
    }
    return false;
  }

  public function resolveDependency($tasks) {
    $results = array();
    $counter = 0;
    foreach ($tasks as $task) {
      if(
        isset($task['dependency']) &&
        $task['dependency'] === 'afterPrevious'
      ) {
        $task['startDate'] = strtotime(
          '+ 1 day',
          $results[$counter - 1]['endDate']
        );
        $dateRange = new DateRange(
          $task['startDate'],
          strtotime(
            '+ ' . ($task['workload'] - 1) . ' day',
            $task['startDate']
          )
        );
        $businessDays = $this->dateUtil->removeNonBusinessdays(
          $dateRange->extract()
        );
        $task['endDate'] = max($businessDays);
        $results[] = $task;
      } else {
        $dateRange = new DateRange(
          $task['startDate'],
          strtotime(
            '+ ' . ($task['workload'] - 1) . ' day',
            $task['startDate']
          )
        );
        $businessDays = $this->dateUtil->removeNonBusinessdays(
          $dateRange->extract()
        );
        $task['endDate'] = max($businessDays);
        $results[] = $task;
      }
      $counter++;
    }
    return $results;
  }

  public function extractDates($tasks) {
    if (!is_array($this->dates)) {
      $this->dates = array();
    }
    foreach ($tasks as $task) {
      $dateRange = new DateRange(
        $task['startDate'],
        strtotime(
          '+ ' . ($task['workload'] - 1) . ' day',
          $task['startDate']
        )
      );

      $businessDays = $this->dateUtil->removeNonBusinessdays(
        $dateRange->extract()
      );

      $results = array_merge(
        $this->dates,
        $businessDays
      );

      foreach ($results as $result) {
        $this->dates[] = $result;
      }
    }
  }

  public function render() {
    echo $this->contents;
  }
}
