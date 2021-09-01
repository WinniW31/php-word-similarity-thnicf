<div class="container">
    <div class="row">
        <div class="col-xl-3"></div>
        <div class="col-xl-6">
            <div class="card" id="loginbox">
                <div class="card-header">Test Similarity</div>
                <div class="card-body">
                    <?php if (isset($validation)) : ?>
                        <div class="col-12">
                            <div class="alert alert-danger" role="alert">
                                <?= $validation->listErrors() ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div id="login-logo">&nbsp;</div>
                    <form class="" action="<?= base_url('dashboard') ?>" method="post">
                        <div class="form-group">
                            <label for="domain">String Similarity</label>
                            <input type="domain" class="form-control" name="domain" id="domain" placeholder="Domain">
                        </div>
                        <button type="submit" class="btn btn-success">Compare</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-3"></div>
    </div>
    <?php if(isset($result)):?>
      <!-- Thai Sim Result-->
      <h3>Nectec โปรแกรมแนะนำคำที่เกี่ยวข้องและความหมายใกล้เคียง with Thai Wiki</h3>
      <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($simWordThwiki["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->score*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>

    <h3>Nectec โปรแกรมแนะนำคำที่เกี่ยวข้องและความหมายใกล้เคียง with Twitter</h3>
    <table class="table">
      <thead>
        <th scope="col">ลำดับ</th>
        <th scope="col">คำค้นหา</th>
        <th scope="col">คำเสมือน</th>
        <th scope="col">% ความเหมือน</th>
      </thead>
      <tbody>
        <?php $i=0; foreach($simWordTwitter["words"] as $key => $val): $i++;?>
        <tr>
          <td><?php echo $i;?></td>
          <td><?php echo $domain;?></td>
          <td><?php echo $val->word;?></td>
          <td><?php echo number_format($val->score*100,2);?></td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
    <!-- End Thai Sim Result-->

    <!-- Thai Approx Result-->
    <h3>Nectec โปรแกรมแนะนำคำที่สะกดใกล้เคียง with พจนานุกรมราชบัณฑิต</h3>
    <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($approxWordRoyin["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->distance*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>

    <h3>Nectec โปรแกรมแนะนำคำที่สะกดใกล้เคียง with รายชื่อคนไทยยอดนิยม</h3>
    <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($approxWordPerson["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->distance*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>

    <h3>Nectec โปรแกรมแนะนำคำที่สะกดใกล้เคียง with รายชื่ออาหาร</h3>
    <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($approxWordFood["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->distance*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <!-- End Thai Approx Result-->

    <!-- Thai Soundex Result-->
    <h3>Nectec โปรแกรมแนะนำคำตามเสียงอ่าน with พจนานุกรมราชบัณฑิต</h3>
    <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($soundexWordRoyin["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->distance*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>

    <h3>Nectec โปรแกรมแนะนำคำตามเสียงอ่าน with รายชื่อคนไทยยอดนิยม</h3>
    <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($soundexWordPerson["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->distance*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>

    <h3>Nectec โปรแกรมแนะนำคำตามเสียงอ่าน with รายชื่ออาหาร</h3>
    <table class="table">
        <thead>
          <th scope="col">ลำดับ</th>
          <th scope="col">คำค้นหา</th>
          <th scope="col">คำเสมือน</th>
          <th scope="col">% ความเหมือน</th>
        </thead>
        <tbody>
          <?php $i=0; foreach($soundexWordFood["words"] as $key => $val): $i++;?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo $domain;?></td>
            <td><?php echo $val->word;?></td>
            <td><?php echo number_format($val->distance*100,2);?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <!-- End Thai Soundex Result-->

     <h3>Similarity Method</h3>
      <table class="table">
          <thead>
            <th scope="col">ลำดับ</th>
            <th scope="col">คำค้นหา</th>
            <th scope="col">คำสงวน</th>
            <th scope="col">JaroWrinkler</th>
            <th scope="col">Levenshtein</th>
            <th scope="col">Jaccard Index</th>
            <th scope="col">Soresen Dice</th>
            <th scope="col">Hamming Distance</th>
          </thead>
          <tbody>
              <?php $i=0; foreach($result as $key => $value): $i++;?>
              <?php if($i <= 10):?>
                  <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $domain;?></td>
                    <?php foreach($value as $key2 => $x):?>
                      <?php if($key2 != "numeric"):?>
                          <?php $x = ($key2 == "sorensendice" && $x == 1) ? 100 : $x;?>
                          <td><?php echo $x;?></td>
                      <?php endif;?>
                    <?php endforeach;?>
                  </tr>
              <?php else: break;?>
              <?php endif;?>
              <?php endforeach;?>
          </tbody>
      </table>

      <h3>Custom Similarity Method</h3>
       <table class="table">
           <thead>
             <th scope="col">ลำดับ</th>
             <th scope="col">คำค้นหา</th>
             <th scope="col">คำสงวน</th>
             <th scope="col">Custom JaroWrinkler</th>
             <th scope="col">Custom Levenshtein</th>
           </thead>
           <tbody>
               <?php $i=0; foreach($custom as $key => $value): $i++;?>
               <?php if($i <= 10):?>
                   <tr>
                     <td><?php echo $i;?></td>
                     <td><?php echo $domain;?></td>
                     <?php foreach($value as $key2 => $x):?>
                       <?php if($key2 != "percentage"):?>
                           <?php $x = ($key2 == "sorensendice" && $x == 1) ? 100 : $x;?>
                           <td><?php echo $x;?></td>
                       <?php endif;?>
                     <?php endforeach;?>
                   </tr>
               <?php else: break;?>
               <?php endif;?>
               <?php endforeach;?>
           </tbody>
       </table>
  <?php endif; //if isset result?>
</div>
