<?php

include_once "environment.php";

$page = new Pager('Accueil');
$page->headerTitle('Accueil');
$page->pageTitle('Bienvenue sur le site du mariage de Colombe & Olivier');
$page->addCss('css/accueil.css');

?>

<p>
	Sur ce petit site, vous trouverez surtout des informations sur la salle, comment y aller, les bonnes adresses dans les alentours 
	où vous pourrez dormir, ...
</p>

<div id="nous">
	<?php echo Html::img("data/couple.png", array('title' => "Photo des futurs mariés à St-K")); ?>
</div>

<div>
	<h3>Présents sur ce site</h3>
	<ul>
		<li>nos conseils sur la manière de venir, avec ou sans GPS (section <?php echo Html::link('Informations > Comment venir', 'infos.php', array('tab' => 'venir')); ?>);</li>
		<li>nous vous conseillons une liste d'hôtels à proximité de la salle de réception (section <?php echo Html::link('Informations > Où dormir ?', 'infos.php', array('tab' => 'logement')); ?>);</li>
	</ul>
</div>

<div id="oneClickZone" class="alert alert-info">
	<h3>Tout en un clic</h3>
	<ul>
		<li>Toutes les adresses du mariage, c'est <?php echo Html::link('ici', 'infos.php', array('tab' => 'venir')); ?>;</li>
		<li>La liste de mariage est <?php echo Html::link('là', 'infos.php', array('tab' => 'liste')); ?>.</li>
	</ul>

</div>

<?php
$page->render();
?>