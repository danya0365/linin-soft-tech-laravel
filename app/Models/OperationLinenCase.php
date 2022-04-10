<?php

namespace App\Models;

class OperationLinenCase
{

  static $list = [
    ['var' => 'new', 'name' => 'ผ้าเคสใหม่ - New', 'icon' => 'bi-plus-circle-dotted'],
    ['var' => 'edit', 'name' => 'ผ้าเคสแก้ไข - Edit', 'icon' => 'bi-pencil-square']
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

  public static function getEdit()
  {
    foreach (self::$list as $linenCase) {
      if ($linenCase['var'] == 'edit') {
        return $linenCase;
      }
    }
    return null;
  }
}
