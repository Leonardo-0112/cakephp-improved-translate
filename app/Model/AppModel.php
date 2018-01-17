<?php

App::uses('Model', 'Model');

class AppModel extends Model {

	/**
	 * Check if an array has only numeric keys.
	 *
	 * @param  array  $value
	 * @return boolean
	 */
	public function isOnlyNumericIndexed( $value )
	{
		return count(array_filter(array_keys($value),'is_string')) == 0 ? true : false;
	}

	/**
	 * Set up the Model to return all languages.
	 *
	 * @return void
	 */
	public function bindAllTranslations()
	{
		$fields = array();

		foreach( $this->actsAs['ImprovedTranslate'] as $field ) {
			$fields[$field] = "{$field}Translation";
		}

		$this->bindTranslation( $fields, false );
	}

	/**
	 * Organizes data to populate all form fields in many languages.
	 *
	 * @param  array $results
	 * @return void
	 */
	public function setTranslationFields( &$results )
	{
		$numeric = $this->isOnlyNumericIndexed( $results );

		$results = $numeric ? $results : array($results);

		foreach( $results as $i => $result ) {
			foreach( $result[$this->name] as $field => $value ) {
				if( isset( $result["{$field}Translation"] )) {
					foreach( $result["{$field}Translation"] as $translate ) {
						$results[$i]['Translation'][$field][$translate['locale']] = $translate['content'];
					}
					unset($results[$i]["{$field}Translation"]);
				}
			}
		}

		$results = $numeric ? $results : $results[0];
	}

	/**
	 * Method which gets associated Models fields translations based upon $actsAs
	 * property, once CakePHP 2 doesn't do it by default.
	 *
	 * @param  array $results
	 * @return void
	 */
	public function associatedTranslate( &$results )
	{
		$numeric = $this->isOnlyNumericIndexed( $results );

		$results = $numeric ? $results : array($results);

		$modelAndFields = array();
		$modelList = App::objects('model');

		foreach( $results as $result ) {
			foreach( array_keys($result) as $model ) {
				if( in_array( $model, $modelList )) {
					if( isset( $this->{$model}->actsAs['ImprovedTranslate'] )) {
						foreach( $this->{$model}->actsAs['ImprovedTranslate'] as $field ) {
							$modelAndFields[$model][] = $field;
						}
					}
				}
			}
		}

		foreach( $results as $i => $result ) {
			foreach( $modelAndFields as $model => $fields ) {
				if( ! $this->isOnlyNumericIndexed( $result[$model] )) {
					$dbData = $this->{$model}->find('first', array(
						'conditions' => array("{$model}.id" => $result[$model]['id']),
						'recursive' => -1
					));
					foreach( $fields as $field ) {
						$results[$i][$model][$field] = $dbData[$model][$field];
					}
				} else {
					foreach( $result[$model] as $j => $res ) {
						$dbData = $this->{$model}->find('first', array(
							'conditions' => array("{$model}.id" => $res['id']),
							'recursive' => -1
						));
						foreach( $fields as $field ) {
							$results[$i][$model][$j][$field] = $dbData[$model][$field];
						}
					}
				}
			}
		}

		$results = $numeric ? $results : $results[0];
	}
}
