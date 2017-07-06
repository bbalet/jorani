<?php
/**
 * This view show comments for a leave.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.7.0
 */

?>
<h2>Commentaires &nbsp;</h2>

<!--
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
        04/07/2017 : Emilien a dit
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse">
      <div class="accordion-inner">
        Je prend un congé parce que c'est comme ça.
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <h6 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2">
        05/07/2017 : Le status de la demande a été changé : <span class='label label-important' style='background-color: #ff0000;'>Rejetée</span>
      </h6>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
        05/07/2017 : Benjamin a dit
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
        C'est mort.
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
        05/07/2017 : RH a dit
      </a>
    </div>
    <div id="collapseThree" class="accordion-body collapse in">
      <div class="accordion-inner">
        Non ca ne peut pas se faire comme ca!
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <h6 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion">
        05/07/2017 : Le status de la demande a été changé : <span class='label label-warning'>Demandée</span>
      </h6>
    </div>
  </div>
</div>
-->


<div class="accordion" id="accordion">
<?php
$i=1;
foreach ($comments->comments as $comments_item) {
  $date=new DateTime($comments_item->date);
  $dateFormat=$date->format(lang('global_date_format'));

  if($comments_item->type == "comment"){
    echo "<div class='accordion-group'>";
    echo "  <div class='accordion-heading'>";
    echo "    <a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion' href='#collapse$i'>";
    echo "      $dateFormat : $comments_item->author a dit";
    echo "    </a>";
    echo "  </div>";
    echo "  <div id='collapse$i' class=\"accordion-body collapse $comments_item->in\">";
    echo "    <div class='accordion-inner'>";
    echo "      $comments_item->value";
    echo "    </div>";
    echo "  </div>";
    echo "</div>";
  }else if ($comments_item->type == "change"){
    echo "<div class='accordion-group'>";
    echo "  <div class='accordion-heading'>";
    echo "    <h6 class='accordion-toggle' data-toggle='collapse' data-parent='#accordion'>";
    echo "      $dateFormat : Le status de la demande a été changé : ";
    switch ($comments_item->status_number) {
      case 1: echo "<span class='label'>" . lang($comments_item->status) . "</span>"; break;
      case 2: echo "<span class='label label-warning'>" . lang($comments_item->status) . "</span>"; break;
      case 3: echo "<span class='label label-success'>" . lang($comments_item->status) . "</span>"; break;
      default: echo "<span class='label label-important' style='background-color: #ff0000;'>" . lang($comments_item->status) . "</span>"; break;
    }
    echo "    </h6>";
    echo "  </div>";
    echo "</div>";
  }
  $i++;
}

 ?>
 </div>
