<?php

namespace PhpGantt;

class DateUtil {
  /**
   * Non businessdays.
   */
  public $nonBusinessdays = array();

  /**
   * String of each days.
   */
  public $days = array(
    'Sun.', 'Mon.', 'Tue.', 'Wed.', 'Thu.', 'Fri.', 'Sat.'
  );

  public function __construct($nonBusinessdays) {
    $this->nonBusinessdays = $nonBusinessdays;
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
}
