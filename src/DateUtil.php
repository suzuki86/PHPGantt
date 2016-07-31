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

  public function __construct($nonBusinessdays, $dayOff) {
    $this->nonBusinessdays = $nonBusinessdays;
    $this->dayOff = $dayOff;
  }

  public function isToday($date) {
    if ($date !== strtotime(date('Y/m/d'))) {
      return false;
    }
    return true;
  }

  public function isBusinessday($date) {
    if (
      in_array($date, $this->nonBusinessdays) ||
      in_array((int)date('w', $date), $this->dayOff)
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
}
