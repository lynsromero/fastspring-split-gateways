/**
 * FastSpring Split Gateways - Admin scripts.
 *
 * Conditional field visibility (data-show-if), multi-select reordering and
 * the Connection Test / Generate Secret AJAX actions.
 *
 * @package fastspring-split-gateways
 */
(function ($) {
	'use strict';

	var params = window.wc_fs_admin_params || { ajax_url: '', nonce: '', messages: {} };

	function getPrefix() {
		return $('#wc_fs_prefix').val() || '';
	}

	function getControl($el) {
		var showIf = $el.data('show-if');
		if (!showIf || typeof showIf !== 'object') {
			return $();
		}
		var key = Object.keys(showIf)[0];
		return $('[name="' + getPrefix() + key + '"]');
	}

	function getExpected($el) {
		var showIf = $el.data('show-if');
		if (!showIf || typeof showIf !== 'object') {
			return undefined;
		}
		return showIf[Object.keys(showIf)[0]];
	}

	function getControlValue($control) {
		if (!$control.length) {
			return undefined;
		}
		if ($control.is(':checkbox')) {
			return $control.prop('checked');
		}
		if ($control.is('select[multiple]')) {
			return $control.val() || [];
		}
		return $control.val();
	}

	function shouldShow($el) {
		var expected = getExpected($el);
		var value = getControlValue(getControl($el));
		if (typeof expected === 'boolean') {
			return value === expected;
		}
		if ($.isArray(value)) {
			return $.inArray(String(expected), value) !== -1;
		}
		return String(value) === String(expected);
	}

	function updateConditionalFields() {
		$('[data-show-if]').each(function () {
			var $el = $(this);
			if (!getControl($el).length) {
				return;
			}
			$el.closest('tr').toggle(shouldShow($el));
		});
	}

	function reorderSelect($control) {
		var $options = $control.find('option');
		$options.sort(function (a, b) {
			if (a.selected === b.selected) {
				return 0;
			}
			return a.selected ? -1 : 1;
		}).appendTo($control);
	}

	function showMessage($button, text, className) {
		var $row = $button.closest('tr');
		$row.find('.wc-fs-message').remove();
		var $msg = $('<span class="wc-fs-message"></span>').text(text);
		if (className) {
			$msg.addClass(className);
		}
		$row.find('fieldset').append($msg);
	}

	function connectionTest() {
		var $button = $('.wc-fs-connection-test');
		$button.prop('disabled', true);
		showMessage($button, params.messages.connection_test);
		$.post(params.ajax_url, {
			action: 'fssg_connection_test',
			_ajax_nonce: params.nonce
		}).done(function (response) {
			if (response && response.success) {
				showMessage($button, response.data.message, 'success');
			} else {
				showMessage($button, (response && response.data && response.data.message) || params.messages.connection_fail, 'error');
			}
		}).fail(function () {
			showMessage($button, params.messages.connection_fail, 'error');
		}).always(function () {
			$button.prop('disabled', false);
		});
	}

	function generateSecret() {
		var $button = $('.wc-fs-generate-secret');
		$button.prop('disabled', true);
		showMessage($button, params.messages.generate_secret);
		$.post(params.ajax_url, {
			action: 'fssg_generate_secret',
			_ajax_nonce: params.nonce
		}).done(function (response) {
			if (response && response.success) {
				$('[name="' + getPrefix() + 'webhook_secret"]').val(response.data.secret);
				showMessage($button, params.messages.secret_generated, 'success');
			} else {
				showMessage($button, (response && response.data && response.data.message) || params.messages.connection_fail, 'error');
			}
		}).fail(function () {
			showMessage($button, params.messages.connection_fail, 'error');
		}).always(function () {
			$button.prop('disabled', false);
		});
	}

	$(function () {
		var prefix = getPrefix();
		updateConditionalFields();

		$(document).on('change', '[name^="' + prefix + '"]', function () {
			var $this = $(this);
			if ($this.is('select[multiple]')) {
				reorderSelect($this);
			}
			updateConditionalFields();
		});

		$(document).on('select2:select select2:unselect', 'select[multiple]', function () {
			reorderSelect($(this));
			updateConditionalFields();
		});

		$(document).on('click', '.wc-fs-connection-test', connectionTest);
		$(document).on('click', '.wc-fs-generate-secret', generateSecret);
	});
})(jQuery);
