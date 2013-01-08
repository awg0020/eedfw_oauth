<?php
class Eedfw_oauth
{
	
	public function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->model('Eedfw_oauth_settings_model');
		$this->EE->load->model('Eedfw_oauth_access_tokens_model');
		$this->EE->load->model('Eedfw_oauth_providers_model');
	}
	
	public function authorization_url() 
	{
		$member_id = $this->EE->TMPL->fetch_param('member_id', $this->EE->session->userdata('member_id'));
		if ($this->EE->session->userdata('member_id') == 0) return FALSE;

		$provider = $this->EE->Eedfw_oauth_providers_model->short_name($this->EE->TMPL->fetch_param('provider'))->row_array();
		show_error($this->EE->lang->line('error_couldnt_load_provider_settings'));	

		$data = array(
			'client_id' 		=> $provider['client_id'],
			'response_type' 	=> 'code',
			'redirect_uri'		=> $provider['redirect_uri'],
			'scope'				=> $provider['scope'],
			);
		$query = http_build_query($data);
		return $provider['authorization_url']."?".$query;
	}
	
	public function access_token() 
	{
		$member_id = $this->EE->TMPL->fetch_param('member_id', $this->EE->session->userdata('member_id'));
		if ($this->EE->session->userdata('member_id') == 0) return FALSE;
		
		$provider = $this->EE->Eedfw_oauth_providers_model->short_name($this->EE->TMPL->fetch_param('provider'))->row_array();
		if (empty($provider)) show_error(lang("error_couldnt_load_provider_settings"));
		
		$access_token = $this->EE->Eedfw_oauth_access_tokens_model->get($provider['provider_id'], $this->EE->session->userdata('member_id'));
		if (!empty($access_token)) {
			if (time() > $access_token['modified_date'] + $access_token['expires_in']) {
				$access_token = $this->refresh_access_token($provider['short_name'], $access_token['member_id'], $access_token['refresh_token']);
			}
			return $access_token['access_token'];
		} else {
			return FALSE;
		}
	}
	
	public function auth_callback() 
	{
		$provider = $this->EE->Eedfw_oauth_providers_model->short_name($this->EE->input->get('provider'))->row_array();
		if (empty($provider)) show_error(lang("error_couldnt_load_provider_settings"));
		
		if ($this->EE->session->userdata('member_id') != 0) {
			$data = array(
				'client_id' 		=> $provider['client_id'],
				'client_secret'		=> $provider['client_secret'],
				'grant_type'		=> 'authorization_code',
				'redirect_uri'		=> $provider['redirect_uri'],
				'code'				=> $this->EE->input->get('code'),
				'format'			=> $provider['response_type'],
			);
			
			$query_string = http_build_query($data);
			$ch = curl_init($provider['access_token_url'] . "?" . $query_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
			$response = curl_exec($ch);

			switch ($provider['response_type']) {
				case "urlencoded":
					parse_str($response, $response);
				default:
					$response = json_decode($response, TRUE);
			}

			$access_token['member_id'] = $this->EE->session->userdata('member_id');
			$access_token['provider_id'] = $provider['provider_id'];
			$access_token['access_token'] = $response[$provider['response_variable_name_access_token']];
			$access_token['expires_in'] = $response[$provider['response_variable_name_expires']];
			$this->EE->Eedfw_oauth_access_tokens_model->set($access_token);

			$this->EE->functions->redirect($this->EE->session->tracker[2]);
		}
	}

	public function deauth_callback() {
		$provider = $this->EE->Eedfw_oauth_providers_model->short_name($this->EE->input->get('provider'))->row_array();
		if (empty($provider)) show_error(lang("error_couldnt_load_provider_settings"));
		
		die();
	}

	public function get_api_meetup_value() 
	{
		$data = array(
			"fields" => "email",
			"member_id" => "self",
			"access_token" => $this->access_token(),
		);
		
		$url = "https://api.meetup.com/2/members?" . http_build_query($data);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch));
		print_r($response);	
	}
	
	public function get_facebook_api_value()
	{
		$data = array(
			"access_token" => $this->access_token(),
		);

		$url = "https://graph.facebook.com/me?" . http_build_query($data);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch));
		print_r($response);		
	}

	private function refresh_access_token($short_name, $member_id, $refresh_token) 
	{
		$provider = $this->EE->Eedfw_oauth_providers_model->short_name($short_name)->row_array();
		if (empty($provider)) show_error(lang("error_couldnt_load_provider_settings"));

		$data = array(
			'client_id' 		=> $provider['client_id'],
			'client_secret'		=> $provider['client_secret'],
			'grant_type'		=> 'refresh_token',
			'refresh_token'		=> $refresh_token,
			'format'			=> $provider['response_type'],
		);

		$ch = curl_init($provider['refresh_access_token_url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		
		switch ($provider['response_type']) {
			case "urlencoded":
				parse_str($response, $response);
			default:
				json_decode($response, TRUE);
		}
		
		$access_token['member_id'] = $member_id;
		$access_token['provider_id'] = $provider['provider_id'];
		$access_token['access_token'] = $response[$provider['response_variable_name_access_token']];
		$access_token['expires_in'] = $response[$provider['response_variable_name_expires']];

		$this->EE->Eedfw_oauth_access_tokens_model->set($access_token);

		return $access_token;
	}

}