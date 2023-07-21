<?php
if($product->get_rating_count() > 0){
	echo wc_get_rating_html( $product->get_average_rating() );
}