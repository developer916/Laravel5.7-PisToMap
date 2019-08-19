@extends('layouts.app')
 
@section('content')

	<!--<div style="margin-top:50px; width: 100%; height: 800px;">
		{!! Mapper::render() !!}
	</div>-->

	<div class="row align-items-center justify-content-center">
		<div class="col-6" style="margin-top: 20px;">
			<select class="form-control" id="colors">
				<option value="#FF0000">Red</option>
				<option value="#00FF00">Green</option>
			</select>
		</div>
		<div id="map" style="margin-top:30px;width:100%;height:800px;"></div>
		<input id="pi_id" type="hidden" value="{{ $pi_id }}"/>
		<div class="col-md-6 text-center" style="margin-bottom: 20px; margin-top: 20px;">
			<textarea class="form-control" id="description" placeholder="Description..." rows="5">{{$description}}</textarea>
		</div>
		<div class="col-md-12 text-center" style="margin-bottom: 20px;">
			<button class="mt-3 mr-3 btn btn-success" onclick="OnSavePiLocations()" id="saveBtn">Save</button>
			<button class="mt-3 mr-3 btn btn-danger" onclick="OnClearPiLocations()" id="clearBtn">Clear</button>
		</div>

		<!--<button class="mt-3 mr-3 btn btn-danger" onclick="OnDeletePiLocations()">Delete</button>-->
	</div>
	
	<script type="text/javascript">

	var poly = [];

	var colors = [];
	var max_no;
	var isClicked = false;

	function initMap() {                            
		
		var center = { lat: 36.1671829223633, lng: -119.994453430176 };

		var pi_locs = <?= $pi_locs ?>;
		var get_colors = <?= $get_colors ?>;
		max_no = {{ $max_no }};
		//console.log("max_no " + pi_locs);
		console.log("max_no " + max_no);

		if(pi_locs.length > 0) {
			center.lat = pi_locs[0]["lat"];
			center.lng = pi_locs[0]["lng"];
		}

		if(get_colors.length >0){
		    for(var i= 0; i<get_colors.length; i++){
		        colors[i+1] = get_colors[i];
			}
		}

		var map = new google.maps.Map(
			document.getElementById('map'), { 
			center: center,
			zoom: 17,
			mapTypeId: google.maps.MapTypeId.SATELLITE,
			scaleControl: false,
			disableDoubleClickZoom: true
		});

		var path = [];
		var isClosed = false;
		for(var i = 1; i <= max_no; i++) {

			poly[i] = new google.maps.Polyline({
				map: map,
				path: [],
				strokeColor: colors[i],
				strokeOpacity: 1.0,
				strokeWeight: 3 
			});
			path[i] = poly[i].getPath();

            pi_locs.forEach(function(pi_loc) {
                if (pi_loc["no"] == i){
                    path[i].push(new google.maps.LatLng(pi_loc["lat"], pi_loc["lng"]));
                }

            });

			poly[i].setPath(path[i]);
		}
		
		google.maps.event.addListener(map, 'click', function (clickEvent) {
			isClicked = true;
			max_no++;
			var getCurrentColor = $("#colors").val();
			poly[max_no] = new google.maps.Polyline({
				map: map,
				path: [],
				strokeColor: getCurrentColor,
				strokeOpacity: 1.0,
				strokeWeight: 3 
			});
			path[max_no] = poly[max_no].getPath();
			poly[max_no].setPath(path[max_no]);
            colors[max_no] = getCurrentColor;

		});

		google.maps.event.addListener(map, 'mousemove', function (event) {
			if (isClicked) {
				poly[max_no].getPath().push(event.latLng);
			}
		});

		google.maps.event.addDomListener(document, 'keyup', function (e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if (code === 27) {
				isClicked = false;
			}
		});
		
	}

	function OnSavePiLocations() {
		
		//if (max_no >= 1) {
			var polys = [];
			for(var i = 1; i <= max_no; i++) {	
				var temp = {};
				JSON.parse(JSON.stringify(poly[i].getPath().getArray())).forEach(function(element) {
					var temp = {};
					temp.no = i;
					temp.lat = element["lat"];
					temp.lng = element["lng"];
					temp.color = colors[i];
					polys.push(temp);
				});
			}
			var polysJSON = JSON.stringify(polys);

			var pi_id = document.getElementById("pi_id").value;
			var description = $("#description").val();
			$("#saveBtn").attr("disabled" , true);
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				type:'POST',
				url:'storePi',
				data:{pi_id:pi_id, polysJSON:polysJSON, description : description},
				success:function(data){
                    $("#saveBtn").attr("disabled" , false);
					if(data.success)
						toastr.success(data.success, 'Success', {timeOut: 2000});
					else
						toastr.error("Whoops! Something wrong", 'Error', {timeOut: 2000});
				}
			});
		//} else 
			//toastr.info("No Pi locations to save", 'Info', {timeOut: 2000});
	}

//	function OnClearPiLocations() {
//
//            OnClearPiLocationsAndDescriptionAjax();
//
//	}

	function OnClearPiLocations(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var pi_id = document.getElementById("pi_id").value;
        $("#clearBtn").attr("disabled" , true);
        $.ajax({
            type:'POST',
            url:'deletePi',
            data:{pi_id:pi_id},
            success:function(data){
                $("#clearBtn").attr("disabled" , false);
                if(data.success){
                    toastr.success(data.success, 'Success', {timeOut: 2000});
                    if (max_no >= 1) {
                        for (var i = 1; i <= max_no; i++) {
                            poly[i].setPath([]);
                        }
                        max_no = 0;
                    }
					$("#description").val("");
				} else {
                    toastr.error("Whoops! Something wrong", 'Error', {timeOut: 2000});
				}
            }
        });
	}

//	function OnDeletePiLocations() {
//		if (max_no >= 1) {
//			$.ajaxSetup({
//				headers: {
//					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//				}
//			});
//
//			var pi_id = document.getElementById("pi_id").value;
//			$.ajax({
//				type:'POST',
//				url:'deletePi',
//				data:{pi_id:pi_id},
//				success:function(data){
//					if(data.success)
//						toastr.success(data.success, 'Success', {timeOut: 2000});
//					else
//						toastr.error("Whoops! Something wrong", 'Error', {timeOut: 2000});
//
//					for(var i = 1; i <= max_no; i++)
//						poly[i].setPath([]);
//					max_no = 0;
//				}
//			});
//		} else
//			toastr.info("No Pi locations to delete", 'Info', {timeOut: 2000});
//	}

	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCFRvN_32EWOGz7V9ujWWmANwufLtWmQ-U&callback=initMap" async defer></script>


@endsection