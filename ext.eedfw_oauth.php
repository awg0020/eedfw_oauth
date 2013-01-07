<?php
class Eedfw_oauth_ext 
{
	public $name = "Oauth";
	public $version = "1.0.0";
	public $description = "";
	public $settings_exist = "n";
	public $docs_url = "http://eedfw.us/addons/oauth";

	public function __construct() {
		$this->EE =& get_instance();
	}
	
	public function settings() {
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->name);
	}

	public function activate_extension() {
		$hooks = array('member_member_register'	=> 'member_member_register');

		foreach ( $hooks as $hook => $method )
		{
			//sessions end hook
			$data = array('class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize(array()),
				'priority'	=> 10,
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);
			$this->EE->db->insert('extensions', $data);
		}
	}

	/**
	* Disables the extension by removing it from the exp_extensions table.
	*
	* @return void
	*/
	function disable_extension() {
		$this->EE->db->delete('extensions', array('class' => __CLASS__));
	}
}