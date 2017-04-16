$(function() {
	var getDistrict = function(type, name, parent, child) {
		var district = $("select[name=" + parent + "]");
		var pid = $(district).find("option:selected").val();
		console.log(pid);
		var url = Think.U('Admin/District/list_by_type', 'type=' + type
				+ '&pid=' + pid);

		$.get(url, function(response) {
			$("select[name=" + name + "] option").remove();
			var data = response.info;
			for ( var i = 0; i < data.length; i++) {
				$("select[name=" + name + "]").append(
						'<option value="' + data[i].id + '">' + data[i].name
								+ '</option>');
			}

			if (child != '') {
				$("select[name=" + child + "]").trigger('change');
			}
		});

	};

	$("select[name=district_id]").change(function() {
		getDistrict(2, 'city_id', 'district_id', 'city_id');

	});

	$("select[name=city_id]").bind('change', function() {
		getDistrict(3, 'store_id', 'city_id', '');
	});
});