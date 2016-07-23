<?php

namespace PhpGantt;

class HtmlBuilder {
  public $dateUtil;

  public function __construct($dateUtil) {
    $this->dateUtil = $dateUtil;
  }

  public function buildTableHeader($dates) {
    $html = '';

    // Build row of month.
    $html .= '<tr>' . PHP_EOL;
    $html .= '<td class="project_name"></td>';
    $html .= '<td class="task_name"></td>';
    $html .= '<td class="assignee"></td>';
    foreach ($dates as $date) {
      $html .= '<td class="cell_header' . $this->getBusinessDayClass($date) . '">' . date('m', $date) .'</td>' . PHP_EOL;
    }
    $html .= '</tr>';

    // Build row of dates.
    $html .= '<tr>' . PHP_EOL;
    $html .= '<td class="project_name"></td>';
    $html .= '<td class="task_name"></td>';
    $html .= '<td class="assignee"></td>';
    foreach ($dates as $date) {
      $html .= '<td class="cell_header' . $this->getBusinessDayClass($date) . '">' . date('d', $date) .'</td>' . PHP_EOL;
    }
    $html .= '</tr>';

    // Build row of days.
    $html .= '<tr>' . PHP_EOL;
    $html .= '<td class="project_name"></td>';
    $html .= '<td class="task_name"></td>';
    $html .= '<td class="assignee"></td>';
    foreach ($dates as $date) {
      $html .= '<td class="cell_header' . $this->getBusinessDayClass($date) . '">' . $this->dateUtil->days[date('w', $date)] . '</td>' . PHP_EOL;
    }
    $html .= '</tr>';
    return $html;
  }

  public function getBusinessDayClass($date) {
    return ($this->dateUtil->isBusinessday($date)) ? ' businessday' : ' nonbusinessday';
  }
}
