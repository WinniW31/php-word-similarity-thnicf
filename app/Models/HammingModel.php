<?php

namespace App\Models;

use CodeIgniter\Model;


class HammingModel extends Model
{
  public function hammingDist($str1, $str2)
  {
      $i = 0; $count = 0;
      while (isset($str1[$i]) != '')
      {
          if ($str1[$i] != $str2[$i])
              $count++;
          $i++;
      }
      return $count;
  }

}
