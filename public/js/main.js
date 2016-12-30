var MatFunnel = new Object();

MatFunnel.DATA_LATLNG = false;
MatFunnel.PARAM = false;

window.onload = function(){
	var map;
	var limit;
	
	//resize();
	//window.addEventListener('resize', resize);
	
	var infowindow = new google.maps.InfoWindow({
		content: '',
		size: new google.maps.Size(50, 50),
		position: { lat: 0, lng: 0 }
	});
	
	$.ajax({
		url: adr_ctr + 'Main/getMapLimit',
		async: false,
		type: 'post',
		success: function(result){
			try{
				limit = JSON.parse(result);
			} catch (exception){
				limit = result;
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		} 
	});
	
	var option = {
			center: new google.maps.LatLng((limit.n + limit.s) / 2, (limit.e + limit.w) / 2),
			zoom: 13,
			minZoom: 13,
			scrollwheel: true,
			//zoomControl: false,
			//rotateControl: false,
			//panControl: false,
			//scaleControl: false,
			disableDefaultUI: true,
			//dragableCursor: "default"
	};
	
	var map_div = document.getElementById('map_canvas');
	
	map = new google.maps.Map(map_div, option);
	
	/*
	google.maps.event.addDomListener(window, 'resize', function(){
		var center = map.getCenter();
		google.maps.event.trigger(map, 'resize');
		map.setCenter(center);
	});
	*/
	
	google.maps.event.addListener(map, 'click', function(event){
		var latitude = event.latLng.lat();
		var longitude = event.latLng.lng();
		
		if (MatFunnel.DATA_LATLNG){
			if (MatFunnel.PARAM)
				console.log('lat=' + latitude + '&lng=' + longitude);
			else
				console.log(latitude, longitude);
		}
		
		var content = "";
		
		if (latitude < limit.n && longitude < limit.e && latitude > limit.s && longitude > limit.w)
			content += "<a onclick='addSpot(" + latitude + "," + longitude + ",0);'>spot 새로만들기</a><br>" ;
		content += "<a onclick='recommendSpot(" + latitude + "," + longitude + ");'>주변 spot 추천</a><br>";
		
		infowindow.setContent(content);
		infowindow.setPosition(event.latLng);
		infowindow.open(map);
	});
	
	/*
	google.maps.event.addListener(map, 'zoom_changed', function(){
		// impl.
	});
	*/
	
	google.maps.event.addListener(map, 'center_changed', function(){
	//google.maps.event.addListener(map, 'idle', function(){
		var latitude = map.center.lat();
		var longitude = map.center.lng();
		
		for (var i = 0; i < 2; ++i){
			if (latitude > limit.n)
				map.setCenter({lat: limit.n - limit._e, lng: longitude});
			
			if (latitude < limit.s)
				map.setCenter({lat: limit.s + limit._e, lng: longitude});
			
			if (longitude > limit.e)
				map.setCenter({lat: latitude, lng: limit.e - limit._e});
			
			if (longitude < limit.w)
				map.setCenter({lat: latitude, lng: limit.w + limit._e});
		}
	});
	
//	google.maps.event.addListener(map, 'center_changed', function(){
//		//setTimeout(centerChangedEvent, 100);
//		var t = centerChangedEvent();
//		alert(t);
//	});
	
	setBorder(map, limit);
	setMarker(map, infowindow);
}

/*
//화면의 사이즈가 변할때 작동.
function resize(){
	$('#main-content').css('height', window.innerHeight + 'px');
}
*/

/*
function centerChangedEvent(){
	var latitude = map.center.lat();
	var longitude = map.center.lng();
	
//	if (latitude > N)
//		map.setCenter({lat: N - _e, lng: longitude});
//	
//	if (latitude < S)
//		map.setCenter({lat: S + _e, lng: longitude});
//	
//	if (longitude > E)
//		map.setCenter({lat: latitude, lng: E - _e});
//	
//	if (longitude < W)
//		map.setCenter({lat: latitude, lng: W + _e});
	
	
	if (latitude > N)
		latitude = N - _e;
	
	if (latitude < S)
		latitude = S + _e;
	
	if (longitude > E)
		longitude = E - _e;
	
	if (longitude < W)
		longitude = W + _e;
	
	console.log(latitude, longitude);
	
	map.setCenter({lat: latitude, lng: longitude});	
}
*/

function setBorder(map, limit){
	var vertex_arr = [
		new google.maps.LatLng(limit.n, limit.w),
		new google.maps.LatLng(limit.n, limit.e),
		new google.maps.LatLng(limit.s, limit.e),
		new google.maps.LatLng(limit.s, limit.w),
		new google.maps.LatLng(limit.n, limit.w),
	];
	
	var border = new google.maps.Polyline({
		path: vertex_arr,
		strokeColor: "#FF9900",
		strokeOpacity: 1.0,
		strokeWeight: 2
	});
	
	border.setMap(map);
}

function setMarker(map, infowindow){
	var SIZE = 48;
	
	var purpose = $("#purpose").val();
	var kind = $("#kind").val();
	
	$.ajax({
		url: adr_ctr + 'Spot/getMarkerData',
		async: false,
		data: {
			purpose: purpose,
			kind: kind
		},
		type: 'post',
		success: function(result){
			try{
				result = JSON.parse(result);
			} catch (exception){}
			
			if (result.code == 200){
				var adr_s3 = $("#adr_s3").val();
				
				var shape = {
						coord:	[	0, 0, 			// upper left
						          	0, SIZE, 		// lower left
						          	SIZE, SIZE, 	// lower right
						          	SIZE, 0	],		// upper right
						type:	'poly'
				};
				
				var markers = [];
				
				for (var i = 0; i < result.data.length; ++i){
					var spot = result.data[i];
					
					var latLng = new google.maps.LatLng(spot.latitude, spot.longitude);
					
					var image = {
							url: adr_s3 + 'spot/' + spot.img,
							size: new google.maps.Size(SIZE, SIZE),
							//origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(SIZE >> 1, SIZE >> 1),
							scaledSize: new google.maps.Size(SIZE, SIZE)
					};
					
					var marker = new google.maps.Marker({
						position: latLng,
						map: map,
						icon: image,
						shape: shape,
						title: spot.name,
						zIndex: 4,			// temp
						optimized: false	// temp
					});
					
					google.maps.event.addListener(marker, 'click', (function(idx, chk){
						return function(event){
							if (chk != 1){
								var content =	"<a onclick='openSpot(" + idx + ");'>spot 보기</a><br>" +
												"<a onclick='addSpot(" + event.latLng.lat() + "," + event.latLng.lng() + "," + idx + ");'>spot 만들기</a><br>" +
												"<a onclick='recommendSpot(" + event.latLng.lat() + "," + event.latLng.lng() + ");'>주변 spot 추천</a><br>";
							}
							else{
								var content =	"<a onclick='openSpotList(" + idx + ");'>spot 목록 보기</a><br>" +
												"<a onclick='addSpot(" + event.latLng.lat() + "," + event.latLng.lng() + "," + idx + ");'>spot 추가하기</a><br>" +
												"<a onclick='recommendSpot(" + event.latLng.lat() + "," + event.latLng.lng() + ");'>주변 spot 추천</a><br>";
							}
							
							infowindow.setContent(content);
							infowindow.setPosition(event.latLng);
							infowindow.open(map);
			            };
			        })(spot.idx, spot.is_cluster));
					
					markers.push(marker);
				}
				
				var markerCluster = new MarkerClusterer(map, markers, {imagePath: adr_ctr + 'img/m'});
				
				google.maps.event.addListener(markerCluster, "clusterclick", function(cluster){
					// impl.
				});
			}
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('맛지도 불러오기에 실패했습니다.\n서버 관리자에게 문의하세요.');
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function openSpot(idx){
	location.href = adr_ctr + 'Spot/index?idx=' + idx;
}

function openSpotList(idx){
	var purpose = $("#purpose").val();
	var kind = $("#kind").val();
	
	$.ajax({
		url: adr_ctr + 'Spot/clusterIndex',
		async: false,
		data: {
			idx: idx,
			purpose: purpose,
			kind: kind,
			adr_ctr: adr_ctr,
			adr_s3: adr_s3
		},
		type: 'post',
		success: function(result){
			$("#popup_background").show();
			
			$("#popup_cluster").html(result);
			$("#popup_cluster").show();
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function closeSpotList(){
	$("#popup_cluster").hide();
	$("#popup_background").hide();
}

function addSpot(lat, lng, base_idx){
	if (loginStateCheck() != 1)
		alert("로그인이 필요한 기능입니다.");
	else
		location.href = adr_ctr + "Spot/writeIndex?lat=" + lat + "&lng=" + lng + "&base=" + base_idx;
}

function recommendSpot(lat, lng){
	if (loginStateCheck() != 1)
		alert("로그인이 필요한 기능입니다.");
	else
		location.href = adr_ctr + "Recommendation/collaborative?lat=" + lat + "&lng=" + lng;
}
