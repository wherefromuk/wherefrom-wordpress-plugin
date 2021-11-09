<?php
function getParentsTree($term, $exclude = true) {
	$categoriesToExclude = get_option('wherefrom_categories_to_exclude', array());

	if ($term->parent > 0) {
		$parentTerm = get_term_by("id", $term->parent, "product_cat");
		// if this needs to be ignored, return only the parents
		if (in_array($term->slug, $categoriesToExclude) && $exclude) return getParentsTree($parentTerm, $exclude);

		// if the parent is not backlisted, can be added to the chain
		$parentTermString = getParentsTree($parentTerm, $exclude);
		if ($parentTermString !== '') {
			return $parentTermString." >> ".$term->name;
		}else{
			return $term->name;
		}

	} else  if ($exclude) {
		if (! in_array($term->slug, $categoriesToExclude)) {
			return $term->name;
		}else{
			return '';
		}
	} else { 
		return $term->name;
	}
}

# Will return all categories of a product, including parent categories
function wherefrom_getAllCategoriesForProduct($productId) {
	$categoryTerms = get_the_terms( $productId, 'product_cat' );
	$categories = [];

	foreach ($categoryTerms as $term) {
		$categories[] = getParentsTree($term);
	}

	// dedupe categories
	$cleanedCategories = array_filter($categories, function($v)use($categories){
		if(count(preg_grep("/" . preg_quote($v, "/") . "/", $categories)) > 1)
			return FALSE;
		return TRUE;
	});

	return $cleanedCategories;
}

function wherefrom_getAllCategories() {
	$args = array(
		'taxonomy'   => "product_cat",
		'number'     => $number,
		'orderby'    => $orderby,
		'order'      => $order,
		'hide_empty' => $hide_empty,
		'include'    => $ids
	);

	$categoryTerms = get_terms($args);

	$categories = array();

	foreach ($categoryTerms as $term) {
		$categories[$term->slug] = getParentsTree($term, false);
	}

	asort($categories);
	return $categories;
}
?>