<?php
/*
Plugin Name: Advanced Google Maps Shortcode
Plugin URI: http://wordpress.org/plugins/advanced-google-maps-shortcode
Description: Really advanced Google Maps shortcode. Map styling, multiple markers, markers clustering and more.
Version: 0.2
Author: Patryk Kachel
Author URI: http://www.limestreet.pl
License: GPLv2
*/

class AdvancedGoogleMapsShortcode {

    function __construct() {
        // Adding shortcode [googlemap].
        add_shortcode( 'googlemap', array( $this, 'googlemap') );
        // Adding button to TinyMCE editor.
        add_action('admin_head', array( $this, 'add_tinymce_button') );
    }

    // Handles shortcode.
    function googlemap( $attr ) {
        
        //var_dump($attr);
        
        $patterns = array();
        $patterns[0] = '/{:/';
        $patterns[1] = '/:}/';
        //$patterns[2] = '/\<br\/\>/';
        $replacements = array();
        $replacements[0] = '[';
        $replacements[1] = ']';
        //$replacements[2] = '';
        $attr = preg_replace($patterns, $replacements, $attr);
        
        // Default values.
        $defaults = array(
            'width'  => '',
            'height' => '400',
            'zoom'   => 12,
            'scrollwheel' => 0,
            'markers' => '[]',
            'center' => '[]',
            'iconurl' => plugin_dir_url( __FILE__ ) . 'img/pin.png',
            'styles' => '[]',
            'maptype' => 'ROADMAP',
            'clustergridsize' => '50'
        );

        // Merging user values and defaults.
        extract( shortcode_atts( $defaults, $attr ) );

        // Shortcode output.
        $output = '';

        $zoom    = esc_js( $zoom );
        $width   = esc_attr( $width );
        $height  = esc_attr( $height );
        $scrollwheel = esc_js( $scrollwheel );
        $iconurl = esc_url( $iconurl );
        $maptype = esc_js( strtoupper( $maptype ) );
        $center = esc_js( $center );
        $clustergridsize = absint( $clustergridsize );
        //$styles = esc_js( $styles );
        //$markers = esc_js( $markers );
        //var_dump($attr);
        
        // Unique shortcode id - support for multiple maps on one page.
        $map_id = 'agms_'.md5( $markers . $zoom . $center );

        // We are adding scripts on page only once and only when shortcode used.
        static $script_added = false;
        if( $script_added == false ) {
            $output .= '<script type="text/javascript"
            src="http://maps.google.com/maps/api/js"></script>';
            $output .= '<script type="text/javascript"
            src="' . plugin_dir_url( __FILE__ ) . 'js/markerclusterer.min.js"></script>';
            $script_added = true;
        }

    // Google map javascript code.
    
    $output .= <<<CODE
    <div id="$map_id"></div>
    
    <script type="text/javascript"> 

var locations = $markers;
var center = $center;

//console.log(locations);

var centerLat = (center.length ? center[0] : locations[0][2]);
var centerLng = (center.length ? center[1] : locations[0][3]);
            
function $map_id() {
    
  var mapOptions = {
    zoom: $zoom,
    scrollwheel: $scrollwheel,
    center: new google.maps.LatLng(centerLat, centerLng),
    mapTypeId: google.maps.MapTypeId.$maptype
  }
  
  var $map_id = new google.maps.Map(document.getElementById('$map_id'), mapOptions);
  
  var styles = $styles;

$map_id.setOptions({styles: styles});
  
  setMarkers($map_id, $markers);
  
}
            
function setMarkers($map_id, locations) {
  // Add markers to the map

  // Marker sizes are expressed as a Size of X,Y
  // where the origin of the image (0,0) is located
  // in the top left of the image.

  // Origins, anchor positions and coordinates of the marker
  // increase in the X direction to the right and in
  // the Y direction down.
  var image = {
    url: '$iconurl',
    // This marker is 21 pixels wide by 32 pixels tall.
    size: new google.maps.Size(32, 60),
    // The origin for this image is 0,0.
    origin: new google.maps.Point(0,0),
    // The anchor for this image is in the middle of the bottom 16,60.
    anchor: new google.maps.Point(16, 60)
  };
  // Shapes define the clickable region of the icon.
  // The type defines an HTML &lt;area&gt; element 'poly' which
  // traces out a polygon as a series of X,Y points. The final
  // coordinate closes the poly by connecting to the first
  // coordinate.
  var shape = {
      coords: [1, 1, 1, 60, 32, 60, 32 , 1],
      type: 'poly'
  };
  
  var infowindow = new google.maps.InfoWindow({
      content: ''
  });
  
  var markers = [];
  
  for (var i = 0; i < locations.length; i++) {
    var singleMarker = locations[i];
    var myLatLng = new google.maps.LatLng(singleMarker[2], singleMarker[3]);
    var marker = new google.maps.Marker({
        position: myLatLng,
        map: $map_id,
        icon: image,
        shape: shape,
        title: singleMarker[0],
        desc: singleMarker[1],
        zIndex: i
    });

    google.maps.event.addListener(marker, 'click', function() {
          var contentString = '<div class="agms-google-map-info"><h5>' + this.title + '</h5>' + '<p>' + this.desc + '</p></div>';
        infowindow.setContent(contentString);
        infowindow.open($map_id,this);
    });
    
    markers.push(marker);
    
  }
  
  var mcOptions = {gridSize: $clustergridsize, maxZoom: 15};
  var mc = new MarkerClusterer($map_id, markers, mcOptions);
}

google.maps.event.addDomListener(window, 'load', $map_id());              
            
    </script>
    
    <style type="text/css">
    #$map_id {
        width: {$width}px;
        height: {$height}px;
    }
    </style>
    
CODE;

    return $output;
    }

    function add_tinymce_button() {
        global $typenow;
        
        // check user permissions
        if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            return;
        }
        
        // verify the post type
        if( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
            return;
        }
        
        // check if WYSIWYG is enabled
        if ( get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
            add_filter('mce_buttons', array( $this, 'register_tinymce_button' ) );
        }
    }
    
    function add_tinymce_plugin($plugin_array) {
        $plugin_array['agms_tinymce_button'] = plugin_dir_url( __FILE__ ) . 'js/tinymce-button.min.js';
        return $plugin_array;
    }
    
    function register_tinymce_button($buttons) {
        array_push($buttons, 'agms_tinymce_button');
        return $buttons;
     }
    
}

new AdvancedGoogleMapsShortcode;