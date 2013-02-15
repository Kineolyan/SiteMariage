$(function() {
	var loader = new library.Loader(['Filtre catégories']);

	new library.Filtre($('#searchBar'), $('#innerCategoriesTable tbody tr'));
	loader.load('Filtre catégories');
});