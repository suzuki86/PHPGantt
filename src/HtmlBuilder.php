<?php

namespace PhpGantt;

use DateRange\DateRange;

class HtmlBuilder {
  public $dateUtil;

  /**
   * String to fill cells.
   */
  public $marker = '#';

  public function __construct($dateUtil) {
    $this->dateUtil = $dateUtil;
  }

  public function buildTableHeader($dates, $tasks) {
    $html = '';
    $html .= '<table class="table_header">';
    $html .= '<tbody>';

    for($x = 1; $x <= 3; $x++) {
      $html .= '<tr>';
      $html .= '<td class="project_name padding">&nbsp;</td>';
      $html .= '<td class="task_name padding">&nbsp;</td>';
      $html .= '<td class="assignee padding">&nbsp;</td>';
      $html .= '</tr>';
    }

    // Build row of gantt.
    foreach ($tasks as $task) {
      $taskDateRange = new DateRange(
        $task['startDate'],
        strtotime('+ ' . ($task['workload'] - 1) . ' day', $task['startDate'])
      );

      $taskDates = $this->dateUtil->removeNonBusinessdays($taskDateRange->extract());

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
      $html .= '</tr>' . PHP_EOL;
    }

    $html .= '</tbody>';
    $html .= '</table>';
    return $html;
  }

  public function buildTableBody ($dates, $tasks) {
    $html = '';
    $html .= '<div class="table_body_wrapper">';
    $html .= '<table class="table_body">' . PHP_EOL;
    $html .= '<tbody>' . PHP_EOL;
    $html .= '<tr>' . PHP_EOL;
    foreach ($dates as $date) {
      $html .= '<td class="cell_header' . $this->getBusinessDayClass($date) . '">' . date('m', $date) .'</td>' . PHP_EOL;
    }
    $html .= '</tr>' . PHP_EOL;

    $html .= '<tr>' . PHP_EOL;
    foreach ($dates as $date) {
      $html .= '<td class="cell_header' . $this->getBusinessDayClass($date) . '">' . date('d', $date) .'</td>' . PHP_EOL;
    }
    $html .= '</tr>' . PHP_EOL;

    $html .= '<tr>' . PHP_EOL;
    foreach ($dates as $date) {
      $html .= '<td class="cell_header' . $this->getBusinessDayClass($date) . '">' . $this->dateUtil->days[date('w', $date)] . '</td>' . PHP_EOL;
    }
    $html .= '</tr>' . PHP_EOL;

    // Build row of gantt.
    foreach ($tasks as $task) {
      $taskDateRange = new DateRange(
        $task['startDate'],
        strtotime('+ ' . ($task['workload'] - 1) . ' day', $task['startDate'])
      );

      $taskDates = $this->dateUtil->removeNonBusinessdays($taskDateRange->extract());

      foreach ($dates as $date) {
        if ($this->dateUtil->isToday($date)) {
          $class_for_today = ' today';
        } else{
          $class_for_today = '';
        }
        if (in_array($date, $taskDates)) {
          $html .= '<td class="cell filled' . $this->getBusinessDayClass($date) . $class_for_today . '">' . $this->marker . '</td>' . PHP_EOL;
        } else {
          $html .= '<td class="cell' . $this->getBusinessDayClass($date) . $class_for_today . '"></td>' . PHP_EOL;
        }
      }
      $html .= '</tr>' . PHP_EOL;
    }

    $html .= '</tbody>' . PHP_EOL;
    $html .= '</table>' . PHP_EOL;
    $html .= '</div>' . PHP_EOL;
    return $html;
  }

  public function build($dates, $tasks) {
    $html .= '';

    $html .= '<div class="phpgantt">';
    $html .= '<div class="phpgantt_container">';
    $html .= '<div class="phpgantt_table_wrapper">';

    $html .= $this->buildTableHeader($dates, $tasks);
    $html .= $this->buildTableBody($dates, $tasks);

    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
  }

  public function getBusinessDayClass($date) {
    return ($this->dateUtil->isBusinessday($date)) ? ' businessday' : ' nonbusinessday';
  }
}
