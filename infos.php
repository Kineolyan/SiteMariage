<?php

include_once "environment.php";

$page = new Pager('Infos');
$page->headerTitle('Informations');
$page->pageTitle('Informations pratiques');
$page->addJs(array('javascript/maps.js', 'javascript/infos.js'));
$page->addCss("css/infos.css");

$VARS->setDefaultGet('tab', 'venir');

?>

<div id="pageInfos">
<!--   <p>Les trucs importants à savoir :
    <ul>
      <li>Il y aura un transport organisé vous ramener Mantes-la-Jolie, gare ou hôtel.</li>
    </ul>
  </p> -->

  <div class="tabbable">
    <?php Html::tabNavigation(array(
      'venir' => "Comment venir ?",
      'logement' => "Où dormir ?",
      'reception' => "Sur place",
      'liste' => "Liste de mariage"
    )); ?>
    <div class="tab-content">
      <div class="tab-pane <?php Html::activateTab('venir'); ?>" id="venir">
        <!-- Comment venir -->
        <?php include_once "vues/include/venir.php"; ?>
      </div>

      <div class="tab-pane <?php Html::activateTab('logement'); ?>" id="logement">
      	<!-- Où se loger -->
        <?php include_once "vues/include/loger.php"; ?>
      </div>

      <div class="tab-pane <?php Html::activateTab('reception'); ?>" id="reception">
        <!-- Sur place -->
        <?php include_once "vues/include/sur_place.php"; ?>
      </div>

      <div class="tab-pane <?php Html::activateTab('liste'); ?>" id="liste">
        <!-- Liste de mariage -->
        <?php include_once "vues/include/liste_mariage.php"; ?>
      </div>
    </div>
  </div>
</div>

<?php
$page->render();
?>