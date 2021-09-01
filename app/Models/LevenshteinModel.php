<?php

namespace App\Models;

use CodeIgniter\Model;


class LevenshteinModel extends Model
{
  public function utf8_to_extended_ascii($str, &$map)
  {
      // find all multibyte characters (cf. utf-8 encoding specs)
      $matches = array();
      if (!preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches))
          return $str; // plain ascii string

      // update the encoding map with the characters not already met
      foreach ($matches[0] as $mbc)
          if (!isset($map[$mbc]))
              $map[$mbc] = chr(128 + count($map));

      // finally remap non-ascii characters
      return strtr($str, $map);
  }

  //Find the number of Edit, Delete, Insert
  public function levenshteinMethod($s1, $s2)
  {
      $charMap = array();
      $s1 = $this->utf8_to_extended_ascii($s1, $charMap);
      $s2 = $this->utf8_to_extended_ascii($s2, $charMap);
      $strlen = strlen($s1)+strlen($s2);


      return number_format((1-(levenshtein($s1, $s2)/$strlen))*100,2);
  }

}
