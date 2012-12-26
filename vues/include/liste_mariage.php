<p>
	Ah, la fameuse liste de mariage !<br/>
	Et bien, la liste de mariage est gérée par le Printemps. Vous pourrez donc faire vos dons directement depuis le web.
	En cas de difficulté, vous pourrez toujours trouver de l'aide auprès de nos familles (rendez-vous sur la page 
	<?php echo Html::link('Contacts', 'contact.php', array('title' => 'Page avec tous les adresses des contacts')) ?>).
</p>

<h2>Que contient la liste</h2>
<p>
	Pour que tout le monde y trouve son compte, notre liste de mariage contient aussi bien des cadeaux classiques (verrerie, linge, ...) que des parties de notre voyage de noces.
	Et oui, en allant à la Réunion et l'île Maurice, ça ne va pas manquer d'excursions exotiques et occasions romantiques (comme un survol de l'île de la Réunion en hélicoptère.
	 Youhou, on est des aventuriers).
</p>

<h2>Comment participer à la liste de mariage</h2>

<p>
	La procédure se fait intégralement en ligne, sur le site <?php echo Html::link('listes.printemps.fr', 'http://listes.printemps.fr'); ?>.<br/>
	Pour un accès direct au formulaire pour faire un cadeau, c'est via le lien qui suit : 
		<?php echo Html::link('Faire un cadeau', 'http://listes-mariage.printemps.com/espace_invites/dons_recherche.aspx', array('title' => 'Accès direct à notre liste')); ?>.
	En cas de problème, reportez-vous à la <?php echo Html::link('procédure détaillée', '#procedure_detaillee'); ?>.<br/>
	Là, indiquez la date du mariage, le <b>13 avril 2013</b>, et <b>Peyrusse</b> comme nom.<br/>
	Ca y est, vous avez accès à la liste !<br/>
	Merci beaucoup d'avance pour vos cadeaux.
</p>

<h2>Procédure détaillée</h2>
<p id="procedure_detaillee" class="accordion">
	<div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#procedure_detaillee" href="#step1">
        1. Se rendre sur le site
      </a>
    </div>
    <div id="step1" class="accordion-body collapse">
      <div class="accordion-inner">
        Pour vous rendre sur le site, le plus simple est de cliquer sur le lien suivant <?php echo Html::link('Accès au site de la liste', 'http://listes.printemps.fr'); ?><br/>
        Sinon, vous pouvez entrer l'adresse suivante dans votre navigateur préféré : <b>http://listes.printemps.fr</b>.<br/>
        Une fois rendu sur la page, cliquez sur la section <b>Faire un cadeau</b>, puis <b>Cadeaux > offrir</b>.
      </div>
    </div>
  </div>

	<div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#procedure_detaillee" href="#step2">
        2. Trouver la liste sur la site
      </a>
    </div>
    <div id="step2" class="accordion-body collapse">
      <div class="accordion-inner">
      	<p>
	        Une fois sur le formulaire, entrez la date de notre mariage dans les champs prévus à cet effet "<b>Date de l'événement</b>":
	        <ul>
	        	<li>jour -> 13</li>
	        	<li>mois -> avril</li>
	        	<li>année -> 2013</li>
	        </ul>
	        Dans le champ "<b>Nom de famille</b>", remplissez <b>Peyrusse</b>.<br/>
	        Vous pouvez lancer la recherche. Chance pour nous, il y a peu de Peyrusse en France qui se marient le 13 avril.
	      </p>
	      <p>
	      	Une fois la recherche terminée, choisissez notre mariage (s'il y en a plusieurs indiqués), puis cliquez sur "<b>Accéder à la liste</b>".
      </div>
    </div>
  </div>

	<div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#procedure_detaillee" href="#step3">
        3. Choisissez vos cadeaux
      </a>
    </div>
    <div id="step3" class="accordion-body collapse">
      <div class="accordion-inner">
        La liste vous est présentée. Elle contient aussi bien des objets qu'on peut trouver dans les boutiques Printemps que des groupements que nous avons faits,
        dans le cas de parties du voyage de noces.<br/>
        Pour nous offrir quelque chose, sur chaque article, vous pouvez indiquer la quantité que vous voulez donner, ou juste participer au coût total en indiquant
        un montant dans la colonne Montant.<br/>
        Sinon, il y a toujours la possibilité d'une participation libre, dans le premier encadré en haut de la page.
      </div>
    </div>
  </div>

	<div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#procedure_detaillee" href="#step4">
       Etapes suivantes
      </a>
    </div>
    <div id="step4" class="accordion-body collapse">
      <div class="accordion-inner">
      	<p>
	        Nous n'avons pas personnellement complété le processus pour faire un cadeau. Une fois que nous aurons des retours sur la procédure, des sections seront ajoutées.
	      </p>

	      <p>
	      	Je vous rappelle qu'en cas de besoin, vous pouvez toujours contacter nos familles (page <?php echo Html::link('Contacts', 'contact.php'); ?>)
	      </p>
      </div>
    </div>
  </div>
</p>