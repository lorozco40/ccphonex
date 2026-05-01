<?php

namespace FreePBX\modules\Sipstation\Api\Gql;

use GraphQL\Type\Definition\Type;
use FreePBX\modules\Api\Gql\Base;

class Sipstation extends Base {
	public function postInitTypes() {
		$destinations = $this->typeContainer->get('destination');
		$destinations->addType($this->typeContainer->get('sipstationwelcome')->getReference());
	}

	public function initTypes() {
		$ssw = $this->typeContainer->create('sipstationwelcome');
		$ssw->setDescription('Plays back a welcome message');
		$ssw->addFieldCallback(function() {
			return [
				'id' => [
					'type' => Type::id(),
				],
				'description' => [
					'type' => Type::string(),
					"description" => "Description of the welcome message"
				]
			];
		});
	}
}
