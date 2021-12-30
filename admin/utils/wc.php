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

function WHEREFROM_buildProduct ($product) {
	$idField = get_option('wherefrom_id_field', 'SKU' );
	$categoriesToExclude = get_option('wherefrom_categories_to_exclude', array());

	if ( ! wc_product_sku_enabled() && $idField === 'SKU' ) {
		$idField = "ID";
	}

	$id = $idField === 'SKU' ? $product->get_sku() : $product->get_id();

	$terms = get_the_terms( $product->get_id(), 'product_brand' );
	$brand_name = '';
	foreach ( $terms as $term ){
		if ( $term->parent == 0 ) {
			$brand_name = $term->slug;
		}
	}  

	$categoryTerms = get_the_terms( $product->get_id(), 'product_cat' );
	$categories = [];

	$categories = wherefrom_getAllCategoriesForProduct($product->get_id());

	$categoryL1 = array();
	$categoryL2 = array();
	$categoryL3 = array();

	foreach($categories as $category) {
		$categoryChunks = explode(" >> ", $category);

		if ($categoryChunks[0]) {
			$categoryL1[] = $categoryChunks[0];
		}
		if ($categoryChunks[1]) {
			$categoryL2[] = $categoryChunks[1];
		}
		if ($categoryChunks[2]) {
			$categoryL3[] = $categoryChunks[2];
		}
	}

	$categoryL1 = WHEREFROM_mostPopularInArray($categoryL1);
	$categoryL2 = WHEREFROM_mostPopularInArray($categoryL2);
	$categoryL3 = WHEREFROM_mostPopularInArray($categoryL3);
	
	$productData = array(
		"sku" => $id,
		"name" => $product->get_title(),
		"brandName"=> '',
		"description" => $product->get_description(),
		"imageUrl"=> wp_get_attachment_url( $product->get_image_id() ),
		"url" => $product->get_permalink(),
		"category1" => $categoryL1,
		"category2" => $categoryL2,
		"category3" => $categoryL3
	);

	return $productData;
}

function WHEREFROM_postProducts($products) {
	$apiKey = get_option('wherefrom_api_key');
	$url = 'https://wherefrom.org/api/external/'.$apiKey.'/products/sync';
	$ch = curl_init($url);
	$jsonDataEncoded = json_encode(array(
		"products" => $products
	));

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	$result = curl_exec($ch);

	curl_close($ch);
	return $result;
}
?>