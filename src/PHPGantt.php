<?php

require 'vendor/autoload.php';

class PHPGantt {
  public $tasks;
  public $startDate;
  public $endDate;
  public $contents;
  public $dates = [];
  public $dateRange;
  public $nonBusinessdays = [];
  public $days = [
    'Sun.', 'Mon.', 'Tue.', 'Wed.', 'Thu.', 'Fri.', 'Sat.'
  ];

  public function __construct($tasks, $nonBusinessdays) {
    $this->nonBusinessdays = $nonBusinessdays;
    $this->tasks = $this->resolveDependency($tasks);
    $this->extractDates($this->tasks);
    $this->dateRange = new DateRange(min($this->dates), max($this->dates));
    $this->dates = $this->dateRange->extract();
    $this->contents = $this->build();
  }

  public function addTasks($tasks) {
    $this->tasks[] = $tasks;
  }

  public function build() {
    $html = '<table>' . PHP_EOL;

    // Build row of month.
    $html .= '<tr>' . PHP_EOL;
    $html .= '<td></td>';
    $html .= '<td></td>';
    foreach ($this->dates as $date) {
      $businessDayClass = ($this->isBusinessday($date)) ? ' businessday' : ' nonbusinessday';
      $html .= '<td class="' . $businessDayClass . '">' . date('m', $date) .'</td>' . PHP_EOL;
    }
    $html .= '</tr>';

    // Build row of dates.
    $html .= '<tr>' . PHP_EOL;
    $html .= '<td></td>';
    $html .= '<td></td>';
    foreach ($this->dates as $date) {
      $businessDayClass = ($this->isBusinessday($date)) ? ' businessday' : ' nonbusinessday';
      $html .= '<td class="' . $businessDayClass .'">' . date('d', $date) .'</td>' . PHP_EOL;
    }
    $html .= '</tr>';

    // Build row of days.
    $html .= '<tr>' . PHP_EOL;
    $html .= '<td></td>';
    $html .= '<td></td>';
    foreach ($this->dates as $date) {
      $businessDayClass = ($this->isBusinessday($date)) ? ' businessday' : ' nonbusinessday';
      $html .= '<td class="' . $businessDayClass . '">' . $this->days[date('w', $date)] . '</td>' . PHP_EOL;
    }
    $html .= '</tr>';

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
      foreach ($this->dates as $date) {
        $businessDayClass = ($this->isBusinessday($date)) ? ' businessday' : ' nonbusinessday';
        if (in_array($date, $taskDates)) {
          $html .= '<td class="cell filled' . $businessDayClass . '">#</td>' . PHP_EOL;
        } else {
          $html .= '<td class="cell' . $businessDayClass . '"></td>' . PHP_EOL;
        }
      }
      $html .= '</tr>' . PHP_EOL;
    }
    $html .= '</table>';
    return $html;
  }

  public function isBusinessday($date) {
    if (
      in_array($date, $this->nonBusinessdays) ||
      date('w', $date) === '0' ||
      date('w', $date) === '6'
    ) {
      return false;
    }
    return true;
  }

  public function removeNonBusinessdays($dates) {
    $result = array();

    foreach ($dates as $date) {
      if ($this->isBusinessday($date)) {
        $result[] = $date;
      } else {
        $tmp = array_merge($result, $dates);
        $default = 1;
        while (
          !$this->isBusinessday(strtotime('+ ' . $default . ' day', max($tmp)))
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
    $results = [];
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
      $this->dates = [];
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
