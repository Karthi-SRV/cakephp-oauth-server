<?php

App::uses('OAuthAppModel', 'OAuth.Model');
App::uses('String', 'Utility');
App::uses('Security', 'Utility');

/**
 * Client Model
 *
 * @property AccessToken $AccessToken
 * @property AuthCode $AuthCode
 * @property RefreshToken $RefreshToken
 */
class Client extends OAuthAppModel {
/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'client_id';
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'client_id';

/**
 * Secret to distribute when using addClient
 * 
 * @var type 
 */	
	protected $addClientSecret = false;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'isUnique' => array(
				'rule' => array('isUnique'),
			),
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'redirect_uri' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'AccessToken' => array(
			'className' => 'OAuth.AccessToken',
			'foreignKey' => 'client_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'AuthCode' => array(
			'className' => 'OAuth.AuthCode',
			'foreignKey' => 'client_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'RefreshToken' => array(
			'className' => 'OAuth.RefreshToken',
			'foreignKey' => 'client_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * AddClient
 * 
 * Convinience function for adding client, will create a uuid client_id and random secret
 * 
 * @param mixed $data Either an array (e.g. $controller->request->data) or string redirect_uri
 * @return booleen Success of failure
 */
	public function add($data = null) {
		$this->data['Client'] = array();

		if (is_array($data['Client']) && array_key_exists('redirect_uri', $data['Client'])) {
			$this->data['Client']['redirect_uri'] = $data['Client']['redirect_uri'];
		} elseif (is_string($data)){
			$this->data['Client']['redirect_uri'] = $data;
		} else {
			return false;
		}

		//$this->data['Client']['client_id'] = String::uuid();
		$this->data['Client']['client_id'] = base64_encode(uniqid() . substr(uniqid(), 11, 2));

		$this->addClientSecret = OAuthComponent::hash(str_shuffle(String::uuid()));
		$this->data['Client']['client_secret'] = $this->addClientSecret;
		
		pr($this->data);
		exit;

		return $this->save($this->data);
	}
	
	public function beforeSave() {
		$this->data['Client']['client_secret'] = OAuthComponent::hash($this->data['Client']['client_secret']);
		return true;
	}
	
	public function afterSave() {
		if ($this->addClientSecret) {
			$this->data['Client']['client_secret'] = $this->addClientSecret;
		}
		return true;
	}

}