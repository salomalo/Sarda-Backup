<?php

namespace ACP\Column\Post;

use AC;
use ACP\Sorting;

class Modified extends AC\Column\Post\Modified
	implements Sorting\Sortable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'modified' );

		return $model;
	}

}