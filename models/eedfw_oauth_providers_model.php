<?php
require_once(__DIR__.'/eedfw_oauth_model.php');

class Eedfw_oauth_providers_model extends Eedfw_oauth_model
{
	protected $table = "eedfw_oauth_providers";
	protected $table_fields = array(
		'provider_id'	=> array(
			'type'					=> 'int',
			'constraint'			=> 10,
			'unsigned'				=> TRUE,
			'auto_increment' => TRUE,
			),
		'site_id'	=> array(
			'type'			=> 'int',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			),
		'short_name'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'client_id'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '50',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'client_secret'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '50',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'authorization_url'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '100',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'access_token_url'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '100',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'refresh_access_token_url'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '100',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'scope'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'redirect_uri'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '100',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'response_variable_name_expires'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'response_variable_name_access_token'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
	);
	protected $primary_keys = array('provider_id');
	protected $table_keys = array();
	
	public $data = array(
		'short_name' => 'sample_provider',
		'client_id' => 'kg2tbegtc60ubvgbfsv79e7ksf',
		'client_secret' => '924fi8994e8ilnog7akcj6nj8i',
		'authorization_url' => 'https://secure.meetup.com/oauth2/authorize',
		'access_token_url' => 'https://secure.meetup.com/oauth2/access',
		'refresh_access_token_url' => 'https://secure.meetup.com/oauth2/access',
		'scope' => 'email',
		'response_variable_name_access_token' => 'access_token',
		'response_variable_name_expires' => 'expires',
	);
	
	protected $query;

	public function __construct() 
	{
		parent::__construct();
	}
	
	public function get() 
	{
		$this->query = $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'));

		return $this->query->get();
	}
	
	
	public function short_name($short_name) {
		$query = $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'));

		$query->where('short_name', $short_name);
		$data = $query->get();

		return (!empty($data)) ? $data : FALSE;
	}
	

	/**
	 * Returns the redirect uri
	 *
	 * @return string
	 * @author Chris LeBlanc
	 */
	public function get_redirect_uri($short_name) 
	{
		return $this->get_act_url('oauth_callback', array('provider' => $this->data['short_name']));
	}
	
	/**
	 * Aggregate dynamic variables.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function aggregate() 
	{
		parent::aggregate();
		$this->data['redirect_uri'] = $this->get_redirect_uri($this->data['short_name']);
	}
	
}