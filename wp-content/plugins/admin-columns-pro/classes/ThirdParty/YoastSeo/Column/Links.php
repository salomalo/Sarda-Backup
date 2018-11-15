<?php

namespace ACP\ThirdParty\YoastSeo\Column;

use ACP\ThirdParty\YoastSeo;
use ACP\Export;

class Links extends YoastSeo\Column
	implements Export\Exportable {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'wpseo-links' );
	}

	public function export() {
		return new Export\Model\Disabled( $this );
	}

}