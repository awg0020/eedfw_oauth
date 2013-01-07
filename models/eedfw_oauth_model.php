<?php
class Eedfw_oauth_model extends CI_Model {
	
	public $data;
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->dbforge();
	}

	public function create_table() 
	{
		$this->dbforge->add_field($this->table_fields);
		foreach($this->primary_keys as $key) $this->dbforge->add_key($key, TRUE);

		$this->dbforge->add_key($this->table_keys);
		$this->dbforge->create_table($this->table, TRUE);
	}

	public function drop_table() 
	{
		return $this->dbforge->drop_table($this->table);
	}

	public function get_act_url($method, $data = array()) 
	{
		$query = $this->db->from('exp_actions')->where('class', 'Eedfw_oauth')->where('method', $method);
		$data['ACT'] = $query->get()->row()->action_id;
		
		return $this->functions->create_url('?' . http_build_query(array_reverse($data)));
	}

	public function save($keys = array()) 
	{
		$this->aggregate();

		if ($this->db->from($this->table)->where($keys)->count_all_results() > 0) {
			return $this->db->where($keys)->update($this->table, $this->data);
		} else {
			return $this->db->insert($this->table, $this->data); 
		}
	}
	
	public function aggregate() 
	{
		$this->data['site_id'] = $this->config->item('site_id');
	}
}