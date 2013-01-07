<?php
class Eedfw_oauth_mcp {
	private $settings;
	
	public function __construct() {
		$this->EE =& get_instance();
		$this->EE->load->helper('form');
		
		$this->EE->load->model('Eedfw_oauth_settings_model');
		$this->EE->load->model('Eedfw_oauth_providers_model');
		
		$this->settings = $this->EE->Eedfw_oauth_settings_model->get();

	}
	public function index() {
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eedfw_oauth'.AMP.'method=settings');
	}
	
	public function settings() {
		$this->EE->cp->set_variable('cp_page_title', lang('eedfw_oauth_module_name'));
		$this->EE->cp->add_js_script('ui', 'accordion');
		
		if (!empty($_POST)) {
			foreach ($_POST['settings'] as $key => $value) {
				$this->EE->Eedfw_oauth_settings_model->set($key, $value);
			}
			foreach ($_POST['providers'] as $value) {
				$this->EE->Eedfw_oauth_providers_model->data = $value;
				$this->EE->Eedfw_oauth_providers_model->save($value['provider_id']);
			}
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eedfw_oauth'.AMP.'method=settings');
		}

		return $this->EE->load->view(__FUNCTION__, array('settings' => $this->settings, 'providers' => $this->EE->Eedfw_oauth_providers_model), TRUE);
	}

}