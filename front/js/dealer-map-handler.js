var $j = jQuery.noConflict();

        var map,
            markersArray = [],
            markersCoords = [],
            addresses = [],
            popups = [],
            infoPopup,
            geocoder,
            mainIcon = dealer_map.mainIcon,
            youIcon = dealer_map.youIcon,
            myshadow = dealer_map.myshadow,
            find_directions,
            directS,
            directD,
            to_marker,
            map_type = dealer_map.map_type,
            zoomer = parseInt(dealer_map.map_zoom),
            marker_event = dealer_map.marker_event,
            drop_bounce = dealer_map.drop_b,
            gDirect = dealer_map.get_direct,
            noStores = dealer_map.nostores,
            yourPosition = dealer_map.yourlocation,
            error_noS = dealer_map.error_nos,
            error_noD = dealer_map.error_nod,
            error_aD = dealer_map.address_err,
            error_oP = dealer_map.error_onpage,
            num_of_Col = dealer_map.colnum,
            ajax_error = dealer_map.aj_error;
 
    function inite() {

        directS = new google.maps.DirectionsService();
        directD = new google.maps.DirectionsRenderer();

        geocoder = new google.maps.Geocoder();
        infoPopup = new google.maps.InfoWindow({
            content: "",
            maxWidth: 330
        });
        var mapOptions = {
            zoom: zoomer,
            center: new google.maps.LatLng(dealer_map.start_lat, dealer_map.start_lng),
            mapTypeId: map_type
        };
        map = new google.maps.Map(document.getElementById('store_map'),
        mapOptions);

        end_directions();

        add_markers();

        $j('select[id^=custom_field_]').change(codeAddress);
        $j('input[id^=search_filter_]').change(codeAddress);

        $j('#address_search').keypress(function (e) {
            if (e.which == 13) {
                codeAddress();
            }
        });
        fixURLS();
    }
    function find_new_stores(show_dist){
        get_new_stores(map.getCenter().lat(), map.getCenter().lng(), show_dist);
    }

    function add_markers(){
        end_directions();
        //remove old markers
        clearMarkers();
        markersArray.length = 0;
        var bounds = new google.maps.LatLngBounds();
        
        drop_markers(bounds);
        
        if(markersArray.length > 0 && addresses.length > 0){
            map.fitBounds(bounds);
        }
    }

    function drop_markers( bounds ) {
        //add new markers
        for(i=0; i<markersCoords.length;i++){
            var ani = (drop_bounce === 'drop') ? google.maps.Animation.DROP : google.maps.Animation.BOUNCE; 
            var myicon = mainIcon;
            var mydrag = false;

            if(markersCoords[i].id == 0){
                myicon = youIcon;
                mydrag = true;
            }else if(markersCoords[i].marker_pro === "pro"){
                myicon = dealer_map.proIcon;
            }
            var myLatlng = new google.maps.LatLng(markersCoords[i].lat,markersCoords[i].lng);

            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                draggable:mydrag,
                animation:ani,
                id:markersCoords[i].id,
                store_address:markersCoords[i].address,
                icon:myicon,
                shadow:myshadow
            });
            bounds.extend(marker.getPosition());

            if(markersCoords[i].id == 0){
                google.maps.event.addListener(marker, "dragend", function(event) {
                    map.setCenter(event.latLng);
                    find_new_stores(1);
                });
            }

            google.maps.event.addListener(marker, marker_event, function(event) {
                info_popups(this.id);
            });
          
            markersArray.push(marker);
        
        }
    }

    function clearMarkers() {
        for(i=0;i<markersArray.length;i++){
            google.maps.event.clearListeners(markersArray[i], marker_event);
            markersArray[i].setMap(null);
        }
    }

    function get_new_stores(lat, lng, calc_distance){
        var calc_dist = 0;
        var defa = false;
        if(calc_distance == 1){
            calc_dist=1;
        }
        var selectboxes = $j('select[id^=custom_field_]');
        var cf_url = "";
        for(i=0;i<selectboxes.length;i++){
            var selectbox = selectboxes[i];
            var val = $j(selectbox).children("option:selected").text();
            if(val){
                cf_url = cf_url + "&" + encodeURIComponent(selectbox.id) + "=" + encodeURIComponent(val);
            }
        }
        selectboxes = $j('input[id^=search_filter_]:checked');
        var sf_url = "";
        var limit = $j("#limit").val();
        var within_distance = $j("#within_distance").val();
        for(i=0;i<selectboxes.length;i++){
            var selectbox = selectboxes[i];
            sf_url = sf_url + "&" + encodeURIComponent(selectbox.id) + "=1";
        }
       
        $j.ajax({
            type: 'GET', // use $j_GET method to submit data
            url: dealer_map.ajaxurl,
            data: { action: 'dealer_search', // where to submit the data
                    security: dealer_map.dealer_mapnonce,
                    lat: lat,
                    lng: lng,
                    radius: within_distance,
                    limit: limit },
            success:function(data) {
                set_stores(data, defa); // the HTML result of that URL after submit the data
            },
            error: function(errorThrown){
                alert(ajax_error);
            }
        });      
    }

    function set_stores(data, deff){
    	//console.log(data);
        find_directions = false;
        popups = data.popup;
        markersCoords.length=0;
        addresses.length=0;
        //console.log(data.stores);
        if(data.stores !== null && data.stores !== undefined ) {
            clear_Errmsg();
            if(data.you){
                markersCoords.push({lat: data.you.lat, lng: data.you.lng, id: 0, address:'', marker_pro:''});
                find_directions=true;
            }

            for(i=0;i<data.stores.length;i++){
                markersCoords.push({lat: data.stores[i].lat, lng: data.stores[i].lng, id: data.stores[i].store_id, address:data.stores[i].summary, marker_pro:data.stores[i].pin_icon, proseries:data.stores[i].pro});
                addresses.push({id: data.stores[i].store_id, address:data.stores[i].summary, distance: data.stores[i].distance, website: data.stores[i].website, marker_pro:data.stores[i].pin_icon});
            }
            $j(".addresses ul").slideUp(show_addresses(deff)); 
            add_markers();
        } else if(!deff) {
            $j('#store_finded h3').remove();
            $j('#addresses_list ul').remove();
            $j("#addresses_list").html("<ul><li class=\""+num_of_Col+" no_stores_found\"><div class='no_stores_found'>"+noStores+"</div></li></ul>");
            $j("addresses_list ul").slideDown();
            return error_nostores('case1');
        } else {
            return error_nostores('case2');
        }  
    }

    function storefindh3(storesFinded, defa) {
        var storesFind = $j('#store_finded');
        var html = '';
        var within_distance = (defa) ? dealer_map.defrange : $j("#within_distance").val();
        var from_zip = (defa) ? dealer_map.defadress : $j("#address_search").val();
            html = "<h3>"+storesFinded+" "+dealer_map.found+" "+within_distance+" "+dealer_map.units+" "+dealer_map.from+" "+from_zip+"</h3>";
           // console.log(storesFind.length);    
        if ( storesFind.length !== 0 ) {
            $j('#store_finded h3').remove();
            storesFind.append(html);
        } else {    
            storesFind.append(html);  
        }  
    }

    function show_addresses(defa){
        var html = "";
        var storesFinded = '0';
        var directions ="";
        for(i=0;i<addresses.length;i++){
            var website = "";
            var pin_colour = "";
            if(find_directions){
                directions="<div class='directions'><a href='#' onclick='calcRoute("+addresses[i].id+");return false;'>"+gDirect+"</a></div>";
            }
            website="<div class='store_website'><a href='" + addresses[i].website + "' target='_blank'>" + addresses[i].website +"</a></div>";            
            if(addresses[i].marker_pro != "main"){
                pin_colour = " style='background-image:url(\""+dealer_map.proSerie+"\")' ";
            }
            html = html + "<li class=\""+num_of_Col+"\"  "+pin_colour+" onmouseover='hoverStart("+addresses[i].id+")' onmouseout='hoverStop("+addresses[i].id+")'><div class='distance'>"+addresses[i].distance+"</div><a href='#' onclick='info_popups("+addresses[i].id+"); return false;'>"+addresses[i].address+"</a>" + website + directions + "</li>";
        }
        if(addresses.length == 0){
            html = "<li class=\""+num_of_Col+"\"><div class='no_stores_found'>"+noStores+"</div></li>";
        } else {
            storefindh3(addresses.length, defa);
        }

        $j(".addresses ul").html(html);
        $j(".addresses ul").slideDown();
        fixURLS();
    }

    function info_popups(id){
        var marker;
        var directions = "";
        if(find_directions){
            directions="<div class='directions'><a href='#' onclick='calcRoute("+id+");return false;'>"+gDirect+"</a></div>";
        }

        for(i=0; i<markersArray.length; i++){
            if(markersArray[i].id == id && id > 0){
                marker = markersArray[i];
                map.panTo(marker.getPosition());
                if(popups[id] != undefined) { 
                   infoPopup.close();
                   infoPopup.setContent("<div class='gm_popup'>"+popups[id].join('') + directions +"</div>");
                   infoPopup.open(map,marker);
                }
            } else if (markersArray[i].id == id && id == 0){
                marker = markersArray[i];
                map.panTo(marker.getPosition());
                infoPopup.close();
                infoPopup.setContent("<div class='gm_popup'>"+ yourPosition +"</div>");
                infoPopup.open(map,marker);
            }
        }
    }

    function codeAddress(){ 
        var address = $j("#address_search").val();   
        if(address && address !== ''){
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    find_new_stores(1);
                } else {
                    error_nostores('ge_add');
                }
            });
        } else {
            return false;
        }
    }

    function codeDefAddress(def, range, limit){
        var address = dealer_map.defadress;
        if( def === '1') { 
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    var lat = map.getCenter().lat();
                    var lng = map.getCenter().lng();
                    $j.ajax({
                        type: 'GET', // use $j_GET method to submit data
                        url: dealer_map.ajaxurl,
                        data: { action: 'dealer_search', // where to submit the data
                            security: dealer_map.dealer_mapnonce,
                            lat: lat,
                            lng: lng,
                            radius: range,
                            limit: limit },
                        success:function(data) {
                            set_stores(data, def); // the default HTML result 
                        },
                        error: function(errorThrown){
                            alert(ajax_error); // error
                        }
                    });
                } else {
                    alert(error_oP);
                }     
            });
        } else { 
            return; 
        }      
    }

    function calcRoute(id) {
        var from = false;
        var to = false;
        for(i=0; i<markersArray.length; i++){
            if(markersArray[i].id == id){
                to = markersArray[i];
            }else if(markersArray[i].id == 0){
                from = markersArray[i];
            }
        }
        if(from && to){
            to_marker=to;
            var request = {
                origin:from.getPosition(),
                destination:to.getPosition(),
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.IMPERIAL            };

            directS.route(request, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directD.setMap(map);
                    directD.setDirections(result);
                    /* debugging */
                    $j("#direction_destination").html(to_marker.store_address);
                    $j("#directions_steps").html('');
                    var stepCount = 1;
                    if(result.routes.length > 0){
                        var my_route = result.routes[0];
                        var j,k;
                        for(j=0; j<my_route.legs.length; j++){
                            var my_leg=my_route.legs[j];
                            for(k=0; k<my_leg.steps.length; k++){
                                var mystep = my_leg.steps[k];
                                $j("#directions_steps").append("<div class='directions_step'><div class='directions_step_id'>"+stepCount+".</div><div class='directions_instructions'>" + mystep.instructions + "</div><div class='directions_step_distance'>" + mystep.distance.text + "</div><div style='clear:both; height:0px'></div></div>")
                                stepCount++;
                            }
                        }
                    }

                    $j("#addresses_list").slideUp(function (){
                        $j("#directions_text").slideDown();
                    });
                    for(i=0; i<markersArray.length; i++){
                        markersArray[i].setMap(null);
                    }
                    infoPopup.setMap(null);
                }
            });
        }
    }

    function error_nostores(cass) {
        var err = '';
        switch (cass) { 
            case 'case1': 
                err = error_noS;
            break;
            case 'case2': 
                err = error_noD;
            break;
            case 'ge_add': 
                err = error_aD;
            break; }
        $j('#dealer-map-error').text(err).show('slow');
    }

    function clear_Errmsg() {
        if ($j('#dealer-map-error').text() != '')
            $j('#dealer-map-error').text('').hide(); 
    }

    function end_directions(){
        directD.setMap(null);
        for(i=0; i<markersArray.length; i++){
            markersArray[i].setMap(map);
        }
        $j("#directions_text:visible").slideUp(function (){
            $j("#addresses_list:hidden").slideDown();
        });
    }

    function hoverStart(id){
        for(i=0; i<markersArray.length; i++){
            if(markersArray[i].id == id){
                marker=markersArray[i];
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }
        }
    }

    function hoverStop(id){
        for(i=0; i<markersArray.length; i++){
            if(markersArray[i].id == id){
                marker=markersArray[i];
                marker.setAnimation(null);
                
            }
        }
    }

    function fixURLS(){
        var websites = $j(".addresses .store_website a");
        for(i=0; i<websites.length; i++){
            $j(websites[i]).text($j(websites[i]).text().replace("http://www.",""))
        }
    }

    $j(document).ready(inite);
    $j(window).load(function() {
        if (dealer_map.showdef === '1') {
            codeDefAddress( dealer_map.showdef, dealer_map.defrange, dealer_map.deflimit );
        }
    });