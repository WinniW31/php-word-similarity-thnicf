<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Pages;
use App\Models\CustomSimilarityModel;
use App\Models\CSVModel;
use App\Models\NectecSimilarityModel;
use App\Models\LevenshteinModel;
use App\Models\JaroWrinklerModel;
use App\Models\JaccardModel;
use App\Models\SorensenModel;
use App\Models\HammingModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['result'] = [];
        $data['custom'] = [];
        $result = '';
        $pages = new Pages();
        $csModel = new CustomSimilarityModel();
        $nectecSim = new NectecSimilarityModel();
        $LvsSim = new LevenshteinModel();
        $JawSim = new JaroWrinklerModel();
        $JacSim = new JaccardModel();
        $sorSim = new SorensenModel();
        $hamSim = new HammingModel();
        $csv = new CSVModel();
        $filepath = 'assets/files/domain_reserved.csv';
        $data['domain_reserved'] = $csv->parse_csv($filepath);
        $string2 = [];

        if ($this->request->getMethod() == 'post') {
            $domain = ($this->request->getVar('domain') !== false && $this->request->getVar('domain') !="") ? strtolower(trim($this->request->getVar('domain'))) : "";
            $length1 = strlen($domain);
            foreach($data['domain_reserved'] as $x => $y){
              array_push($string2, strtolower(trim($y['word'])));
            }

            $simWordThwiki = $nectecSim->getNectecmethod($domain, "thaiwordsim", "thwiki", 5);
            $simWordTwitter = $nectecSim->getNectecmethod($domain, "thaiwordsim", "twitter", 5);

            $approxWordRoyin = $nectecSim->getNectecmethod($domain, "wordapprox", "royin", 5);
            $approxWordPerson = $nectecSim->getNectecmethod($domain, "wordapprox", "personname", 5);
            $approxWordFood = $nectecSim->getNectecmethod($domain, "wordapprox", "food", 5);

            $soundexWordRoyin = $nectecSim->getNectecmethod($domain, "soundex", "royin", 5);
            $soundexWordPerson = $nectecSim->getNectecmethod($domain, "soundex", "personname", 5);
            $soundexWordFood = $nectecSim->getNectecmethod($domain, "soundex", "food", 5);


            foreach($string2 as $key => $value){
              $length2 = strlen($value);
              $result = $csModel->fstrcmp($domain, $length1, $value, $length2, 0);
              $percentage = number_format($result*100,2);
              $equalLength = ($length1 == $length2) ? true : false;
              //$findStrPOS = $csModel->findStrPOS($domain, $value);
              if($percentage > '50'){
                $data['result'][$key]['word'] = $value;
                $data['custom'][$key]['word'] = $value;
                //คือวิธีการแบบหนึ่งที่ใช้วัดความเหมือนกันระหว่างสองสายอักขระจากจำนวนตัวอักษรร่วม
                //โดยระยะทางจาโรจะใช้หลักการวัดความต่างกัน ของสายอักขระจากจำนวนตัวอักษรที่เหมือนกัน
                //และอยู่ในตำแหน่งใกล้เคียงกัน คือตำแหน่งห่างกันไม่เกินครึ่งหนึ่งของความยาวอักขระสายที่สั้น
                $data['result'][$key]['jawrowrinkler'] = $JawSim->JaroWinkler($domain, $value);
                $data['custom'][$key]['jawrowrinkler'] = $JawSim->CustomJaroWinkler($domain, $value);
                $data['custom'][$key]['percentage'] = $percentage."%";
                $data['custom'][$key]['numeric'] = $percentage;
                //เป็นขั้นตอนวิธีการวัดหาค่าความต่างกันของสายอักขระสองชุด ระหว่างชุดแรกที่เป็นต้นแบบ และ ชุดที่สองที่เป็นชุดเปรียบเทียบ
                //โดยค่าความต่างกันจะวัดจากจำนวนของการที่จะต้องทำการตัดออก แทรก และแทนที่
                //อักขระในชุดที่นำมาเปรียบเทียบจนกระทั่งมีลักษณะเหมือนชุดอักขระที่เป็นต้นแบบทุกประการ
                $data['result'][$key]['levenshtein'] = $LvsSim->levenshteinMethod($domain, $value);
                //Falling under the set similarity domain, the formulae is to
                //find the number of common tokens and divide it by the total number of unique tokens.
                //Its expressed in the mathematical terms
                $data['result'][$key]['jaccard'] = $JacSim->getSimilarityCoefficient($domain, $value);
                //Falling under set similarity, the logic is to find the common tokens,
                //and divide it by the total number of tokens present by combining both sets
                $data['result'][$key]['sorensendice'] = $sorSim->DiceMatch($domain, $value);
                //
                $data['result'][$key]['hamming'] = ($equalLength === true) ? $hamSim->hammingDist($domain, $value) : "diff length";

                usort($data['result'],function($a,$b){
                    return $b['jawrowrinkler']-$a['jawrowrinkler'];
                });
                usort($data['custom'],function($a,$b){
                    return $b['jawrowrinkler']-$a['jawrowrinkler'];
                });
              }
            }
            $data['domain'] = $domain;
            $data['simWordThwiki']   = (array) $simWordThwiki;
            $data['simWordTwitter']  = (array) $simWordTwitter;
            $data['approxWordRoyin'] = (array) $approxWordRoyin;
            $data['approxWordPerson'] = (array) $approxWordPerson;
            $data['approxWordFood'] = (array) $approxWordFood;
            $data['soundexWordRoyin'] = (array) $soundexWordRoyin;
            $data['soundexWordPerson'] = (array) $soundexWordPerson;
            $data['soundexWordFood'] = (array) $soundexWordFood;


        }

        return $pages->view("dashboard", $data);

    }

}
