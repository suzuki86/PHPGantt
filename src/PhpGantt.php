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
   * String to fill cells.
   */
  public $marker = '#';

  /**
   * Filter
   */
  public $filters = array();

  public function __construct($tasks, $nonBusinessdays, $filters = array()) {
    $this->filters = $filters;
    $this->nonBusinessdays = $nonBusinessdays;

    $this->dateUtil = new DateUtil($nonBusinessdays);
    $this->htmlBuilder = new HtmlBuilder($this->dateUtil);

    $this->tasks = $this->resolveDependency($tasks);
    $this->tasks = $this->filterTasks($this->tasks, $this->filters);
    $this->extractDates($this->tasks);
    $this->dateRange = new DateRange(min($this->dates), max($this->dates));
    $this->dates = $this->dateRange->extract();
    $this->contents = $this->build();
  }

  public function addTasks($tasks) {
    $this->tasks[] = $tasks;
  }

  public function build() {
    $html = '<table>';
    $html .= $this->htmlBuilder->buildTableHeader($this->dates);

    // Build row of gantt.
    foreach ($this->tasks as $task) {
      $taskDateRange = new DateRange(
        $task['startDate'],
        strtotime('+ ' . ($task['workload'] - 1) . ' day', $task['startDate'])
      );

      $taskDates = $this->removeNonBusinessdays($taskDateRange->extract());

      $html .= '<tr>' . PHP_EOL;
      $html .= '<td class="project_name">';
      $html .= $task['project'];
      $html .= '</td>';
      $html .= '<td class="task_name">';
      $html .= $task['name'];
      $html .= '</td>';
      $html .= '<td class="assignee">';
      $html .= (isset($task['assignee'])) ? $task['assignee'] : '';
      $html .= '</td>';
      foreach ($this->dates as $date) {
        if ($this->isToday($date)) {
          $class_for_today = ' today';
        } else{
          $class_for_today = '';
        }
        if (in_array($date, $taskDates)) {
          $html .= '<td class="cell filled' . $this->htmlBuilder->getBusinessDayClass($date) . $class_for_today . '">' . $this->marker . '</td>' . PHP_EOL;
        } else {
          $html .= '<td class="cell' . $this->htmlBuilder->getBusinessDayClass($date) . $class_for_today . '"></td>' . PHP_EOL;
        }
      }
      $html .= '</tr>' . PHP_EOL;
    }
    $html .= '</table>';
    return $html;
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

  public function isToday($date) {
    if ($date !== strtotime(date('Y/m/d'))) {
      return false;
    }
    return true;
  }

  public function removeNonBusinessdays($dates) {
    $result = array();

    foreach ($dates as $date) {
      if ($this->dateUtil->isBusinessday($date)) {
        $result[] = $date;
      } else {
        $tmp = array_merge($result, $dates);
        $default = 1;
        while (
          !$this->dateUtil->isBusinessday(strtotime('+ ' . $default . ' day', max($tmp)))
        ) {
          $default++;
        }
        $result[] = strtotime('+ ' . $default . ' day', max($tmp));
        $default = 1;
      }
    }
    sort($result);
    return $result;
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
        $businessDays = $this->removeNonBusinessdays(
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
        $businessDays = $this->removeNonBusinessdays(
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

      $businessDays = $this->removeNonBusinessdays(
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
