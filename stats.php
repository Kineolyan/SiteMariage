<?php

include_once "environment.php";

$page = new Pager('Stats');
$page->headerTitle('Statistiques');
$page->pageTitle('Statistiques sur les invités');

// Statistiques sur les réponses
$DB->select('invites', 'COUNT(*) as total, statut', '', array('groupBy' => 'statut'));
$statutStats = array();
$totalInvites = 0;
while ($statutData = $DB->fetch()) {
	$proportion = intval($statutData['total']);
	$statutStats[$statutData['statut']] = $proportion;
	$totalInvites += $proportion;
}
$DB->endQuery();
?>
<h2>Réponses</h2>
<p>Nombre de réponses reçues:
	<strong><?php echo $statutStats['-1'] + $statutStats['1'], '/', $totalInvites; ?>
	</strong>, dont :
	<ul>
		<li class="stats">Réponses positives :  <span class="stats"><?php echo $statutStats['1'], '/', $totalInvites; ?></span></li>
		<li class="stats">Réponses négatives :  <span class="stats"><?php echo $statutStats['-1'], '/', $totalInvites; ?></span></li>
	</ul>
Réponses attendues: <?php echo $statutStats['0']; ?>
</p>

<h2>Catégories</h2>
<?php
$DB->select('categories JOIN mm_user_categorie ON mm_user_categorie.categorie_id=categories.id',
		'categorie, COUNT(mm_user_categorie.id) as nb', '',
		array('groupBy' => 'categories.id'));
$listeCategories = '';
while ($categorieData = $DB->fetch()) {
	$listeCategories .= "<li class=\"stats\">$categorieData[categorie] : <span class=\"stats\">$categorieData[nb]</span></li>";
}
$DB->endQuery();
?>
<p>Groupements par catégories :
	<ul><?php echo $listeCategories; ?></ul>
</p>

<?php
$page->render();
?>