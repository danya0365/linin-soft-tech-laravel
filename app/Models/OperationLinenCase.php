<?php

namespace App\Models;

class OperationLinenCase
{

  static $list = [
    ['var' => 'new', 'name' => 'ผ้าเคสใหม่', 'icon' => 'bi-plus-circle-dotted'],
    ['var' => 'new', 'name' => 'ผ้าเคสแก้ไข', 'icon' => 'bi-pencil-square']
  ];

  public static function getByVar($var)
  {
    foreach (self::$list as $linenCase) {
      if ($linenCase['var'] == $var) {
        return $linenCase;
      }
    }
    return null;
  }
}
