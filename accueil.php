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

<p>
	En détail :
	<ul>
		<li>nos conseils sur la manière de venir, avec ou sans GPS;</li>
		<li>nous vous conseillons une liste d'hôtels à proximité de la salle de réception;</li>
		<li>des informations pratiques sur l'organisation de la soirée</li>
	</ul>

	En un clic :
	<ul>
		<li>Toutes les adresses du mariage, c'est <?php echo Html::link('ici', 'infos.php', array('tab' => 'venir')); ?></li>
		<li>La liste de mariage est <?php echo Html::link('là', 'infos.php', array('tab' => 'liste')); ?></li>
	</ul>

</p>

<?php
$page->render();
?>