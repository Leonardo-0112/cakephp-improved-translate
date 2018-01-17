<?php

App::uses('TranslateBehavior', 'Model/Behavior');

class ImprovedTranslateBehavior extends TranslateBehavior {

	/**
	 * Organizes data to be saved in many languages.
	 *
	 * @param  Model  $Model
	 * @return boolean
	 */
	public function afterValidate( Model $Model )
	{
		parent::afterValidate( $Model );

		foreach( $Model->data[$Model->name] as $field => $value1 ) {
			if( isset( $Model->data['Translation'][$field] )) {
				foreach( $Model->data['Translation'][$field] as $lang => $value2 ) {
					$Model->data['Translation'][$field][$lang] = $value2 === '' ? $value1 : $value2;
				}
				$Model->data['Translation'][$field][DEFAULT_LANG] = $value1;
				$Model->data[$Model->name][$field] = $Model->data['Translation'][$field];
			}
		}

		unset($Model->data['Translation']);

		return true;
	}
}
