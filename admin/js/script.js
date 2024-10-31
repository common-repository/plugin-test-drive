jQuery(document).ready(function() {
	////////////////////////////////////WP plugins page/////////////////////////

	//add class to tested plugins for background colors control. "prevUntil" requires jquery >= 1.4
	if(jQuery('tr.second').length > 0) {
		jQuery('.plugin-tested-tr').prevUntil('.inactive').andSelf().prev().prev().andSelf().addClass('tested');
	} else {
		jQuery('.plugin-tested-tr').prev().andSelf().addClass('tested');//wp 3.1 and above
	}
	jQuery('.tested input[type=checkbox]').prop('disabled', 'true');//disable option to select tested plugins for bulk actions

	////////////////////////////////////PTD options page/////////////////////////
	var idValue;
	idMethCheck();
	idValCheck();

	function idMethCheck() {
		var methText;
		if (jQuery('#tester-key-method').val() == '0') {
			methText = _ptdL10n.ip;
			idValue = _ptdL10n.CurrentIP;
		}
		else {
			methText = _ptdL10n.user;
			idValue = _ptdL10n.CurrentUserName;
		}
		jQuery('#set-to-current').text(_ptdL10n.set + ' ' + methText);
	}

	function idValCheck() {
		var idVal = jQuery('#tester-key-val').val();
		if (idVal != idValue) {
			jQuery('#set-to-current').addClass('clickable');//ip validation below
			if (!idVal || (jQuery('#tester-key-method').val() == '0' && !idVal.match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$"))) {
				jQuery('#ptd-submit').prop('disabled', true).addClass('no-submit');
				var errorText = (!idVal) ? _ptdL10n.mandatory : _ptdL10n.valid;
				jQuery('.error-submit').text(errorText).show();
			} else {
				selfID(false);
			}
		} else {
			selfID(true);
		}
	}

	//set value of textbox to current ip or user name
	function setToCurrent() {
		idMethCheck();
		jQuery('#tester-key-val').val(idValue);
		jQuery('.error-submit').hide();
		selfID(true);
	}

	jQuery('#set-to-current').click(function() {
		setToCurrent();
	});

	jQuery('#tester-key-method').change(function() {
		setToCurrent();
	});

	jQuery('#tester-key-val').change(function() {
		idValCheck();
	});

	jQuery('#cbToggle').click(function() {
		jQuery(this).parents('tbody').find('.pluginCheck').attr('checked', this.checked);
		jQuery('#toggleSelect').text(!this.checked ? _ptdL10n.check : _ptdL10n.uncheck);
	});

	function selfID(removeSetToCurrLink) {
		jQuery('#ptd-submit').prop('disabled', false).removeClass('no-submit');
		if(removeSetToCurrLink)
			jQuery('#set-to-current').removeClass('clickable');
	}
	
	//correct table width for plugin list in split columns view
	var extra=90, a=jQuery('.first').width()+extra, b=jQuery('.second').width()+extra, c=jQuery('.third').width()+extra;
	jQuery('#plugList').css('width',a+b+c);
	jQuery('.first').attr('width',a);
	jQuery('.second').attr('width',b);
	jQuery('.third').attr('width',c);
})