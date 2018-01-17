<?php

App::uses('AppController', 'Controller');

class LanguagesController extends AppController {

	protected $_validLanguages = array('por','eng','spa');

	public function change( $lang = null )
	{
		if( ! in_array( $lang, $this->_validLanguages )) {
			return $this->set(array(
				'status' => 'ERROR',
				'message' => 'Invalid Language',
				'_serialize' => array('status','message')
			));
		}

		$this->Session->write('Config.language', $lang);

		return $this->set(array(
			'status' => 'OK',
			'message' => 'Language Changed',
			'_serialize' => array('status','message')
		));
	}
}
