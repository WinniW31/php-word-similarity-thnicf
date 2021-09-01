<?php

namespace App\Models;

use CodeIgniter\Model;


class JaccardModel extends Model
{
  public function getSimilarityCoefficient( $item1, $item2)
  {

  	$item1 = array_unique(array_map('trim', str_split( strtolower($item1) )));
  	$item2 = array_unique(array_map('trim', str_split( strtolower($item2) )));
    $arr_intersection = array_intersect( $item2, $item1 );
  	$arr_union = array_unique(array_merge( $item1, $item2 ));
  	$coefficient = count( $arr_intersection ) / count( $arr_union );

  	return number_format($coefficient*100,2);
  }

}
