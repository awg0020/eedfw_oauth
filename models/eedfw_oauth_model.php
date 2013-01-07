<?php
class Eedfw_oauth_model extends CI_Model {
	
	public $data;
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function create_table() 
	{
		$this->load->dbforge();
		$this->dbforge->add_field($this->table_fields);
		$this->dbforge->add_key($this->primary_key, TRUE);
		$this->dbforge->add_key($this->table_keys);
		$this->dbforge->create_table($this->table, TRUE);
	}

	public function drop_table() 
	{
		$this->load->dbforge();
		return $this->dbforge->drop_table($this->name);
	}
	
	public function load_sample_data() 
	{
		$this->db->empty_table($this->table);
		$this->save();
	}
	
	public function get_act_url($method, $data = array()) 
	{
		$query = $this->db->from('exp_actions')->where('class', 'Eedfw_oauth')->where('method', $method);
		$data['ACT'] = $query->get()->row()->action_id;
		
		return $this->functions->create_url('?' . http_build_query(array_reverse($data)));
	}
	
	/**
	 * Save data stored in $data.
	 *
	 * @return result set
	 * @author Chris LeBlanc
	 **/
	public function save($key = "") 
	{
		$this->aggregate();
		
		if (!empty($key)) {
			if ($this->db->from($this->table)->where($this->primary_key, $key)->count_all_results() > 0)
				return $this->db->where($this->primary_key, $key)->update($this->table, $this->data);
			else 
				return $this->db->insert($this->table, $this->data); 
		}
	}
	
	public function aggregate() 
	{
		$this->data['site_id'] = $this->config->item('site_id');
	}
}