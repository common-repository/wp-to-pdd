jQuery(document).ready(function() {
	var $ = jQuery;
	setInterval(function() {
		clear_msgs();
	}, 1000);
	if (all_boxes.accounts.length) {
		$("#box-item").tmpl(all_boxes.accounts).appendTo("#the-list");
	} else {
		$("#box-item-default").tmpl().appendTo("#the-list");
	}
	$("#form-add-box").tmpl().appendTo(".mail-boxes-form");
	$(".select-all-boxes").change(function() {
		if ($(this).is(":checked")) {
			$(".select-all-boxes").attr("checked", true);
			$("[id^='select-box']").attr("checked", true);
		} else {
			$(".select-all-boxes").attr("checked", false);
			$("[id^='select-box']").attr("checked", false);
		}
	});
	$("#doaction").live("click", function() {
		if ($("[id^='select-box']:checked").length == 0) {
			return false;
		}
		if ($("#ymfdp_action").val() == "delete") {
			if (!confirm(message_codes['mass_box_delete'])) {
				return false;
			}
		}
	});
	$(".submitdelete").live("click", function() {
		if (confirm(message_codes['box_delete'])) {
			var formData = new FormData();
			if ($("input[name='update_box[uid]']").val() == $(this).data("uid")) {
				$(".mail-boxes-form").empty();
				$("#form-add-box").tmpl().appendTo(".mail-boxes-form");
			}
			formData.append('delete_box', $(this).data("uid"));
			getUrl("POST", '/wp-admin/admin.php?page=all_boxes', function(e) {
				var data = JSON.parse(e.target.response);
				if (data.success == "error") {
					show_msg("error", message_codes[data.error]);
				} else {
					show_msg("updated", message_codes['ok_delete']);
					$("[data-login='"+data.login+"']").remove();
					if (!$(".box-item").length) {
						$("#box-item-default").tmpl().appendTo("#the-list");
					}
				}
			}, formData);
		}
		return false;
	});
	$(".resetedit").live("click", function() {
		$(".mail-boxes-form").empty();
		$("#form-add-box").tmpl().appendTo(".mail-boxes-form");
	});
	$(".submitedit").live("click", function() {
		$(".mail-boxes-form").empty();
		$("#form-update-box").tmpl([
			{
				uid: $(this).data('uid'),
				login: $(this).data('login'),
				iname: $(this).data('iname'),
				fname: $(this).data('fname'),
				enabled: $(this).data('enabled')
			}
		]).appendTo(".mail-boxes-form");
		return false;
	});
	$("#box-form").live("submit", function() {
		var formData = new FormData(this);
		getUrl("POST", '/wp-admin/admin.php?page=all_boxes', function(e) {
			var data = JSON.parse(e.target.response);
			if (data.success == "error") {
				show_msg("error", message_codes[data.error]);
			} else {
				if ('account' in data) {
					show_msg("updated", message_codes['ok_update']);
					$("#box-item").tmpl([data.account]).insertBefore("tr[data-login='"+data.login+"']");
					$("tr[data-login='"+data.login+"']:last").remove();
					$(".mail-boxes-form").empty();
					$("#form-add-box").tmpl().appendTo(".mail-boxes-form");
				} else {
					$("form")[0].reset();
					show_msg("updated", message_codes['ok_add']);
					if ($(".box-item").length) {
						$("#box-item").tmpl([{uid: data.uid, login: data.login, fio: '', enabled: 'yes', ready: 'no'}]).insertBefore(".box-item:first");
					} else {
						$(".box-item-default").remove();
						$("#box-item").tmpl([{uid: data.uid, login: data.login, fio: '', enabled: 'yes', ready: 'no'}]).appendTo("#the-list");
					}
				}
			}
		}, formData);
		return false;
	});
});

function show_msg(type, msg) {
	jQuery("#msgs").append('<div data-time="'+(new Date().getTime())+'" class="'+type+'"><p>'+msg+'</p></div>');
}

function clear_msgs() {
	jQuery("#msgs > div").each(function(i, item) {
		if (jQuery(item).data("time") < (new Date().getTime() - 5000)) {
			jQuery(item).animate({opacity: "hide"}, "slow", "linear", function() {
				jQuery(item).remove();
			});
		}
	});
}

function getUrl(type, url, calback, formData) {
	var xhr = new XMLHttpRequest();
	xhr.open(type, url, true);
	xhr.onload = calback;
	xhr.send(formData);
}
