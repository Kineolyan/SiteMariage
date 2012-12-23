<?php

include_once "environment.php";

$page = new Pager('Contact');
$page->headerTitle('Nous contacter');
$page->pageTitle('Nous contacter');
$page->addCss('css/contact.css')
?>

<div id="contactBoard">
	<div class="cRow">
		<div class="case colive">
			<p>&nbsp;</p>
		</div>
		<div class="case cbleue">
			<p>
				Pour nous contacter, rien de plus simple: une seule adresse : 
				<a rel="email">ceto[at]<br/>colombeetolivier[point]fr</a>
			</p>
		</div>
		<div class="case crouge">
			<p>&nbsp;</p>
		</div>
	</div>
	<div class="cRow">
		<div class="case corange">
			<p>
				Besoin de contacter les témoins :<br/>
				<a rel="email">temoins[at]<br/>colombeetolivier[point]fr</a>
			</p>
		</div>
		<div class="case">
			<p>&nbsp;</p>
		</div>
		<div class="case cjaune">
			<p>
				La famille Peyrusse, c'est à cette adresse :<br/>
				<a rel="email">peyrusse[at]<br/>colombeetolivier[point]fr</a>
			</p>
		</div>
	</div>
	<div class="cRow">
		<div class="case cnavy">
			<p>&nbsp;</p>
		</div>
		<div class="case cviolet">
			<p>&nbsp;</p>
		</div>
		<div class="case cverte">
			<p>
				Pour les Ribéreau-Gayon, c'est par là :<br/>
				<a rel="email">ribereau[at]<br/>colombeetolivier[point]fr</a></p>
		</div>
	</div>
</div>

<?php
$page->render();
?>