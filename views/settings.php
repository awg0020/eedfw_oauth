<style>
#mainContent .pageContents h2 { margin-bottom: 10px; }
#mainContent .pageContents table.mainTable { margin-bottom: 20px; }
.editAccordion td h4 { font-size: 13px; }
</style>
<script type="text/javascript">
$(function () {
	rebind();
	function rebind() {
		$(".editAccordion > h3").css("cursor", "pointer");
		$(".editAccordion").css("borderTop", $(".editAccordion").css("borderBottom"));
		$(".editAccordion h3").click(function() {
			if ($(this).hasClass("collapsed")) {
				$(this).siblings().show();
				$(this).removeClass("collapsed").parent().removeClass("collapsed");
			}
			else {
				$(this).siblings().hide();
				$(this).addClass("collapsed").parent().addClass("collapsed");
			}
		});

		$('.provider input[name$="[short_name]"]').bind("change keyup input", function () {
			$this = $(this);
			$provider = $this.closest('.provider');
			$provider.find('.short_name').html($this.val());
			// console.log($provider.find('input').attr('name').replace("#^(.+)(\[\w+\])(\[.+)$#", "$1["+$this.val()+"]$3"));
		});
	}
	
	$("#add_provider").click(function (e) {
		$provider = $(".editAccordion").last();
		$clone = $provider.clone();
		$provider.after($clone);

		$clone.find('input').val('');
		$clone.find('.short_name').html('');
		
		var index = $('.provider').length - 1;
		$clone.find('input').each(function() { $(this).attr('name', $(this).attr('name').replace(/^(.+)(\[\w+\])(\[.+)$/, "$1["+index+"]$3")); });

		rebind();
		e.preventDefault();
	});
	
});
</script>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eedfw_oauth'.AMP.'method=settings')?>
<h2>Settings</h2>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<tr><th colspan="2">Global Settings</th></th></tr>

</table>

<h2>OAuth Providers</h2>
<?php $i = 0; ?>
<?php foreach ($providers->get()->result_array() as $provider): ?>
<div class="editAccordion provider">
	<input type="hidden" name="providers[<?php echo $i?>][provider_id]" value="<?php echo $provider['provider_id'] ?>" />
	<h3>Provider (<span class="short_name"><?php echo $provider['short_name'] ?></span>)</h3>
	<table class="mainTable" border="0" cellspacing="0" cellpadding="0" style="margin: 0;">
		<tr>
			<td><label for="short_name">Short Name</label><div class="subtext">A unique short name for you to reference this OAuth provider.</div></td>
			<td><input type="text" name="providers[<?php echo $i?>][short_name]" value="<?php echo $provider['short_name'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="redirect_uri">Redirect URI</label><div class="subtext">Registered redirect_uri for that client ID.</div></td>
			<td><?php echo $providers->get_act_url('auth_callback') ?>&amp;provider=<span class="short_name"><?php echo $provider['short_name'] ?></span></td>
		</tr>
		<tr><td colspan="2"><h4>Your Application Settings</h4></td></tr>
		<tr>
			<td><label for="client_id">Consumer/Client/App ID</label><div class="subtext">The client id for your application. Also referred to as a "key."</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][client_id]" value="<?php echo $provider['client_id'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="client_secret">Consumer/Client/App Secret</label><div class="subtext">The client secret associated with your client ID.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][client_secret]" value="<?php echo $provider['client_secret'] ?>" /></td>
		</tr>
		<tr><td colspan="2"><h4>Request Settings</h4></td></tr>
		<tr>
			<td><label for="authorization_url">Authorization URL</label><div class="subtext">URL provided by your provider for client authorization requests.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][authorization_url]" value="<?php echo $provider['authorization_url'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="access_token_url">Access Token URL</label><div class="subtext">URL provided by your provider for client access token requests.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][access_token_url]" value="<?php echo $provider['access_token_url'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="refresh_access_token_url">Refresh Access Token URL</label><div class="subtext">URL provided by your provider for client access token refresh requests.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][refresh_access_token_url]" value="<?php echo $provider['refresh_access_token_url'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="scope">Scope</label><div class="subtext">A space-delimited list of scopes that identify the resources that your application could access on the user's behalf.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][scope]" value="<?php echo $provider['scope'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="scope">Response Type</label><div class="subtext">Some providers require you to specify the response type when requesting an access token.</div></td>
			<td>
				<select name="providers[<?php echo $i ?>][response_type]">
					<option value=""></option>
					<option value="json" <?php if ($provider['response_type'] == "json") echo "selected" ?>>JSON</option>
					<option value="urlencoded" <?php if ($provider['response_type'] == "urlencoded") echo "selected" ?>>URL Encoded</option>
					<!--<option value="xml">XML</option>-->
				</select>
			</td>
		</tr>
		<tr><td colspan="2"><h4>Response Settings</h4></td></tr>
		<tr>
			<td><label for="scope">Access Token Variable Name</label><div class="subtext">The variable name that contains the access token from the provider's response. Usually access_token.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][response_variable_name_access_token]" value="<?php echo $provider['response_variable_name_access_token'] ?>" /></td>
		</tr>
		<tr>
			<td><label for="scope">Expires Variable Name</label><div class="subtext">The variable name that contains the expires timestamp from the provider's response. Usually expires or expires_in.</div></td>
			<td><input type="text" name="providers[<?php echo $i ?>][response_variable_name_expires]" value="<?php echo $provider['response_variable_name_expires'] ?>" /></td>
		</tr>

	</table>
</div>
<?php $i++; ?>
<?php endforeach ?>

<div class="tableFooter">
	<div class="tableSubmit">
		<div><a href="#" id="add_provider">+ Add Another Provider</a></div>	
	</div>
</div>

<div class="tableFooter">
	<div class="tableSubmit">
		<?php echo form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'submit'));?>
	</div>
</div>	
<?php echo form_close()?>