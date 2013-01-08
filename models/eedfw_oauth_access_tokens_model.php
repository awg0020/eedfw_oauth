<?php
require_once(__DIR__.'/eedfw_oauth_model.php');

class Eedfw_oauth_access_tokens_model extends Eedfw_oauth_model {
	protected $table = "eedfw_oauth_access_tokens";
	protected $table_fields = array(
		'provider_id'	=> array(
			'type'					=> 'int',
			'constraint'			=> 10,
			'unsigned'				=> TRUE,
			),
		'member_id'	=> array(
			'type'					=> 'int',
			'constraint'			=> 10,
			'unsigned'				=> TRUE,
			),
		'access_token'	=> array(
			'type' 					=> 'varchar',
			'constraint'			=> 200,
			'null'					=> FALSE,
			),
		'refresh_token'	=> array(
			'type' 					=> 'varchar',
			'constraint'			=> 200,
			'null'					=> FALSE,
			),
		'expires_in'	=> array(
			'type'					=> 'int',
			'constraint'			=> 10,
			'unsigned'				=> TRUE,
			),
		'modified_date'	=> array(
			'type'					=> 'int',
			'constraint'			=> 10,
			'unsigned'				=> TRUE,
			),
		'token_type'	=> array(
			'type'					=> 'varchar',
			'constraint'			=> 30,
			'null'					=> FALSE,
			'default'				=> 'Bearer',
			),
		);
	protected $primary_keys = array('provider_id', 'member_id');
	protected $table_keys = array();
	
	public function __construct() {
		parent::__construct();
	}
	
	public function set($data) 
	{
		if (empty($data['member_id'])) $data['member_id'] = $this->session->userdata('member_id');
		$data['modified_date'] = time();

		if ($this->db->from($this->table)->where('member_id', $data['member_id'])->where('provider_id', $data['provider_id'])->count_all_results() > 0) {
			return $this->db->where('member_id', $data['member_id'])->where('provider_id', $data['provider_id'])->update($this->table, $data);
		} else {
			return $this->db->insert($this->table, $data); 
		}
	}
	
	public function get($provider_id, $member_id = false) 
	{
		$query = $this->db->from($this->table);

		if ($member_id) {
			$query->where('member_id', $member_id)
				->where('provider_id', $provider_id);
			$data = $query->get()->row_array();

			return (!empty($data)) ? $data : FALSE;
		} else {
			$query->where('provider_id', $provider_id);
			$data = $query->get()->result_array();

			return (!empty($data)) ? $data : FALSE;
		}
	}
	

}