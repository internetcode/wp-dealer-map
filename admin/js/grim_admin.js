jQuery( document ).ready( function( $ ) {
    
    // Initiate admin tabs
    $(function () {
     var tabs = $("#general").tabs({
        activate: function(event, ui){
            //get the active tab index
            var active = $("#general").tabs("option", "active");

            //save it to cookies
            $.cookie("activeTabIndex", active);
        }
    });

    //read the cookie
    var activeTabIndex = $.cookie("activeTabIndex");

    //make active needed tab
    if(activeTabIndex !== undefined) {
        tabs.tabs("option", "active", activeTabIndex);
    }
       
    });

    var mediaUploader;
  $('#csv_file_button').click(function(e) {
    e.preventDefault();
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: grim_admin_vars.upl_title,
      button: {
      text: grim_admin_vars.upl_text
    }, multiple: false });
    mediaUploader.on('select', function() {
      var attachment = mediaUploader.state().get('selection').first().toJSON();
      $('#csv_file').val(attachment.url);
    });
    mediaUploader.open();
  });

  // The "Remove" button (remove the value from input type='hidden')
  $('.remove_image_button').click(function() {
      var answer = confirm(grim_admin_vars.upl_sure);
      if (answer == true) {
          var src = $('#csv_file').val('');
      }
      return false;
  });

  // Make sure the custom error notices can be removed
  $( "#grim-wrap" ).on( "click", "button.notice-dismiss", function() {
      $( this ).closest( 'div.notice' ).remove();
  });
  
  // Click to hide error/success messages
  $('.error_message, .success_message, .info_message_dismiss').click(function () {
     $(this).fadeOut("slow", function () {
      });
  });

  /**
   * Handle the red warning that's shown next to the
   * force zipcode search option if the autocomplete
   * value is changed.
   */
  $( "#grim-search-autocomplete, #grim-force-postalcode" ).change( function() {
      var $info = $( "#grim-force-postalcode" ).parent( "p" ).find( ".grim-info-zip-only" );

      if ( $( "#grim-search-autocomplete" ).is( ":checked" ) && $( "#grim-force-postalcode" ).is( ":checked" ) ) {
          $info.show();
      } else {
          $info.hide();
      }
  });

  $( "#grim-delay-loading" ).change( function() {
      if ( $( this ).is( ":checked" ) ) {
          $( this ).parent( "p" ).find( ".grim-info" ).trigger( "mouseover" );
      } else {
          $( this ).parent( "p" ).find( ".grim-info" ).trigger( "mouseout" );
      }
  });

  $( "#grim-wrap" ).on( "click", function( e ) {
      $( ".grim-info-text" ).hide();
  });

  // Delete DB Table button
  $('#dialog-confirm').dialog({
  autoOpen: false,
  width: 400,
  modal: true,
  resizable: false,
  buttons: {
      'Delete Table': function () {
      $('#delete_db_button_hidden').val('true');
      $(this).dialog('close');
      $('#grim_form').submit();
      },
      'Cancel': function () {
      $(this).dialog("close");
      }
  }
  });
  $('#delete_db_button').click(function (e) {
      $('#dialog-confirm').dialog('open');
  });
  
  $('#show_all').on('click', function() {
     var url = 'admin.php?page=grimdealers/grim_locations';
      window.open(url, '_self'); 
  });
  
  function initgmap() { 
     $('#grim-map').addClass('bigi');
     var geocoder = new google.maps.Geocoder();
     var map = new google.maps.Map(document.getElementById('grim-map'), {
         zoom: 8,
         center: {lat: 32.480285, lng: -95.703372}
     });
     geocodeAddress(geocoder, map);
  }

  function addTofields(results, address) { 
  
    var forM = results.formatted_address.split(','),
        addr = address.replace(/, /g, ' ').trim(),
        valString = forM.slice(0, forM.length-1),
        valSearch = valString.toString().replace(/, /g, ' ').trim(),
        valName = addr.replace(valSearch, ' ');

    if($('#name').val() === '') {
      $('#name').val(valName);
    }

    $('#latitude').val(results.geometry.location.lat().toFixed(6));
    $('#longitude').val(results.geometry.location.lng().toFixed(6));
    $('#active').val('1');

    if(forM.length === 4) {
      $('#address').val(forM[0].trim());
      $('#city').val(forM[1].trim());
      $('#state').val(forM[2].trim().split(' ', 1));
      $('#zip').val(forM[2].trim().slice(3, 8));
      $('#country').val(forM[3].trim());
    } else {
      popufield(results);
    }
  }

  function popufield(results) {

    var addr_comp = results.address_components;

    if(addr_comp.length > 6) {
      $('#address').val(addr_comp[0].long_name+' '+addr_comp[1].long_name+' '+addr_comp[2].long_name);
      $('#city').val(addr_comp[3].long_name);
      $('#state').val(addr_comp[4].long_name);
      $('#zip').val(addr_comp[6].long_name);
      $('#country').val(addr_comp[5].short_name);
    } else { 
      $('#address').val(addr_comp[0].long_name+' '+addr_comp[1].long_name);
      $('#city').val(addr_comp[2].long_name);

      if(addr_comp[4].short_name === 'US') {
        $('#state').val(addr_comp[3].short_name);
      } else {
        $('#state').val(addr_comp[3].long_name);
      }
      
      $('#zip').val(addr_comp[5].long_name);
      $('#country').val(addr_comp[4].short_name);
    }
  }

  function geocodeAddress(geocoder, resultsMap) {

    var addr = $('#address').val(),
        city = $('#city').val(),
        state = $('#state').val(),
        zip = $('#zip').val(),
        address = addr+', '+city+', '+state+', '+zip;

    if( address.length >= 20 ) { 
      geocoder.geocode({'address': address}, function(results, status) {
        if (status === 'OK') {
          resultsMap.setCenter(results[0].geometry.location);
          addTofields(results[0], address);
          var marker = new google.maps.Marker({
            map: resultsMap,
            position: results[0].geometry.location
          });
        } else {
          alert(grim_admin_vars.geo_alert+ ': ' +status);
        }
      });
    } else {
      return false;
    }
  }

  var h_location = location.href.split('?')[1];
  if(grim_admin_vars.api_keys !== '') { 
    if (h_location === 'page=grimdealers%2Flocations_form' || h_location.indexOf('locations_form&id=') != -1) { 
      $('.grim-latlng').on('click', function(e) {
          e.preventDefault();
          initgmap();
      });
    }
  } else {
    return false;
   // alert(grim_admin_vars.api_alert);
  }  
});