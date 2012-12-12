<?php

include_once "environment.php";

$page = new Pager('Infos');
$page->headerTitle('Informations');
$page->pageTitle('Informations pratiques');
$page->addJs(array('javascript/maps.js', 'javascript/infos.js'));
$page->addCss("css/infos.css");

?>

<div id="pageInfos">
  <p>Les trucs importants à savoir :
    <ul>
      <li>Il y aura un transport organisé vous ramener Mantes-la-Jolie, gare ou hôtel.</li>
    </ul>
  </p>

  <div class="tabbable">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab1" data-toggle="tab">Comment venir ?</a></li>
      <li><a href="#tab2" data-toggle="tab">Où dormir</a></li>
      <li><a href="#tab3" data-toggle="tab">Sur place</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab1">
        <!-- Comment venir -->
        <?php include_once "vues/include/venir.php"; ?>
      </div>

      <div class="tab-pane" id="tab2">
      	<!-- Où se loger -->
        <?php include_once "vues/include/loger.php"; ?>
      </div>

      <div class="tab-pane" id="tab3">
      	<!-- Sur place -->
        <?php include_once "vues/include/sur_place.php"; ?>
      </div>
    </div>
  </div>
</div>

<?php
$page->render();
?>