<?php
class Eedfw_oauth_upd {

	public $name = "Eedfw_oauth";
	public $version = "1.0.0";
	public $description = "";
	public $settings_exist = "y";
	public $docs_url = "http://eedfw.us/products/oauth";

	var $settings       = array();

	private $mod_actions = array('auth_callback', 'deauth_callback');

	public function __construct() {
		$this->EE =& get_instance();
		$this->EE->load->library('logger');

		$this->EE->load->model('Eedfw_oauth_settings_model');
		$this->EE->load->model('Eedfw_oauth_providers_model');
		$this->EE->load->model('Eedfw_oauth_access_tokens_model');
	}

	function install() {
		// install module
		$data = array(
			'module_name' => $this->name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
		$this->EE->db->insert('modules', $data);

		// install actions
		$this->EE->db->select('method')
			->from('actions')
			->like('class', $this->name, 'after');
		$existing_methods = array();
		foreach ($this->EE->db->get()->result() as $row) $existing_methods[] = $row->method;
		foreach ($this->mod_actions as $method)	{
			if ( ! in_array($method, $existing_methods)) {
				$this->EE->db->insert('actions', array('class' => $this->name, 'method' => $method));
			}
		}
		
		// install settings
		$this->EE->Eedfw_oauth_settings_model->create_table();
		
		// install providers
		$this->EE->Eedfw_oauth_providers_model->create_table();
		$this->EE->Eedfw_oauth_providers_model->save();
		
		// install providers
		$this->EE->Eedfw_oauth_access_tokens_model->create_table();

		return TRUE;

	}

	function uninstall() {
		$this->EE->db->delete('modules', array('module_name' => $this->name));
		$this->EE->db->like('class', $this->name, 'after')->delete('actions');
		
		$this->EE->Eedfw_oauth_settings_model->drop_table();
		$this->EE->Eedfw_oauth_providers_model->drop_table();
		$this->EE->Eedfw_oauth_access_tokens_model->drop_table();

		return TRUE;
	}
}