var selected_address={'district_id':'大区', 'city_id':'城市', 'store_id': '门店'};

$(function() {
	var getDistrict = function(type, name, parent, child, not_trigger) {
		var district = $("select[name=" + parent + "]");
		var pid = $(district).find("option:selected").val();
		console.log(pid);
		var url = Think.U('Admin/District/list_by_type', 'type=' + type
				+ '&pid=' + pid);

		$.get(url, function(response) {
			$("select[name=" + name + "] option").remove();
			var data = response.info;
			console.log(data);
			
			
			$("select[name=" + name + "]").append('<option value="0">'+selected_address[name]+'</option>');
			if(data != null) {
				
				for ( var i = 0; i < data.length; i++) {
					$("select[name=" + name + "]").append(
							'<option data-tokens="' + data[i].name + '" value="' + data[i].id + '">' + data[i].name
									+ '</option>');
				}
				$("select[name=" + name + "]").selectpicker('refresh')
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
		if($("select[name=store_id]").length == 0) {
			return;
		}
		
		getDistrict(3, 'store_id', 'city_id', '');
	});
});