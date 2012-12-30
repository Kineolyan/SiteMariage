<?php

include_once "environment.php";

$page = new Pager('Stats');
$page->headerTitle('Statistiques');
$page->pageTitle('Statistiques sur les invités');
$page->addCss('css/stats.css');

function createStatsArray() {
	return  array(
		'total' => 0,
		Invite::getStatus(1) => 0,
		Invite::getStatus(-1) => 0,
		Invite::getStatus(0) => 0
	);
}

function sumStats(&$total, $addition) {
	foreach ($addition as $key => $value) {
		$total[$key] += $value;
	}
}

function getStatsForCategorie($categorieId) {
	global $DB;

	$DB->select('categories JOIN mm_user_categorie ON mm_user_categorie.categorie_id=categories.id'
			.	' JOIN invites ON mm_user_categorie.user_id=invites.id',
		'COUNT(mm_user_categorie.id) AS nb, invites.statut AS statut', 
		'mm_user_categorie.categorie_id='.$categorieId,
		array('groupBy' => 'invites.statut'));

	$stats = createStatsArray();

	while ($stat = $DB->fetch()) {
		$count = intval($stat['nb']);

		$stats[Invite::getStatus($stat['statut'])] = $count;
		$stats['total'] += $count;
	}

	$DB->endQuery();

	return $stats;
}

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

<table class="table table-hover">
	<thead>
		<tr>
			<th>Catégorie</th>
			<th><?php echo Invite::getStatus(1);?></th>
			<th><?php echo Invite::getStatus(-1);?></th>
			<th><?php echo Invite::getStatus(0);?></th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
<?php
$globalStats = createStatsArray();

foreach (Categories::getCategories() as $categorieId => $categorie) {
	$stats = getStatsForCategorie($categorieId);

	echo sprintf('<tr> <td>%s</td> <td>%d</td> <td>%d</td> <td>%d</td> <td class="totalCategorie">%d</td> </tr>',
		$categorie, 
		$stats[Invite::getStatus(1)], $stats[Invite::getStatus(-1)], $stats[Invite::getStatus(0)], 
		$stats["total"]);

	sumStats($globalStats, $stats);
}

echo sprintf("<tr class='totalPresence'> <td>Total par présence</td> <td>%d</td> <td>%d</td> <td>%d</td> <td>%d</td> </tr>",
		$globalStats[Invite::getStatus(1)],
		$globalStats[Invite::getStatus(-1)], 
		$globalStats[Invite::getStatus(0)], 
		$globalStats["total"]);
?>
	</tbody>
</table>

<?php
$page->render();
?>