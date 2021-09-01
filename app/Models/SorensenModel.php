<?php

namespace App\Models;

use CodeIgniter\Model;


class SorensenModel extends Model
{
    private function wordLetterPairs($str)
    {
        $allPairs = array();

        // Tokenize the string and put the tokens/words into an array

        $words = str_split($str);

        // For each word
        for ($w = 0; $w < count($words); $w++)
        {
            // Find the pairs of characters
            $pairsInWord = $this->letterPairs($words[$w]);

            for ($p = 0; $p < count($pairsInWord); $p++)
            {
                $allPairs[] = $pairsInWord[$p];
            }
        }

        return $allPairs;
    }


    private function letterPairs($str)
    {
        $numPairs = mb_strlen($str)-1;
        $pairs = array();

        for ($i = 0; $i < $numPairs; $i++)
        {
            $pairs[$i] = mb_substr($str,$i,2);
        }

        return $pairs;
    }


    public function compareStrings($str1, $str2)
    {
        $pairs1 = $this->wordLetterPairs(strtolower($str1));
        $pairs2 = $this->wordLetterPairs(strtolower($str2));

        $intersection = 0;

        $union = count($pairs1) + count($pairs2);

        for ($i=0; $i < count($pairs1); $i++)
        {
            $pair1 = $pairs1[$i];

            $pairs2 = array_values($pairs2);
            for($j = 0; $j < count($pairs2); $j++)
            {
                $pair2 = $pairs2[$j];
                if ($pair1 === $pair2)
                {
                    $intersection++;
                    unset($pairs2[$j]);
                    break;
                }
            }
        }

        return (2.0*$intersection)/$union;
    }

    public function DiceMatch($string1, $string2)
    {
    	if (empty($string1) || empty($string2))
    		return 0;

    	if ($string1 == $string2)
    		return 1;

    	$strlen1 = strlen($string1);
    	$strlen2 = strlen($string2);

    	if ($strlen1 < 2 || $strlen2 < 2)
    		return 0;

    	$length1 = $strlen1 - 1;
    	$length2 = $strlen2 - 1;

    	$matches = 0;
    	$i = 0;
    	$j = 0;

    	while ($i < $length1 && $j < $length2)
    	{
    		$a = substr($string1, $i, 2);
    		$b = substr($string2, $j, 2);
    		$cmp = strcasecmp($a, $b);

    		if ($cmp == 0)
    			$matches += 2;

    		++$i;
    		++$j;
    	}
      
    	return number_format(($matches / ($length1 + $length2))*100,2);
    }

}
