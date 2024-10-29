(function() {
    tinymce.PluginManager.add('agms_tinymce_button', function( editor, url ) {
        editor.addButton( 'agms_tinymce_button', {
            text: 'AGMS',
            title: 'Advanced Google Maps Shortcode',
            icon: false,
            onclick: function() {
                editor.windowManager.open( {
                    title: 'Insert Google Maps Shortcode',
                    body: [{
                        type: 'textbox',
                        name: 'width',
                        label: 'Width'
                    },
                    {
                        type: 'textbox',
                        name: 'height',
                        label: 'Height'
                    },
                    {
                        type: 'textbox',
                        name: 'zoom',
                        label: 'Zoom',
                        value: '12'
                    },
                    {
                        type: 'textbox',
                        name: 'iconurl',
                        label: 'Icon URL'
                    },
                    {
                        type: 'listbox', 
                        name: 'maptype', 
                        label: 'Map Type', 
                        'values': [
                            {text: 'Roadmap', value: 'ROADMAP'},
                            {text: 'Terrain', value: 'TERRAIN'},
                            {text: 'Satellite', value: 'SATELLITE'},
                            {text: 'Hybrid', value: 'HYBRID'}
                        ]
                    },
                    {
                        type: 'textbox',
                        name: 'markers',
                        label: 'Markers',
                        value: 'Some Title, Some Really Nice Description, 40.711811, -73.993803',
                        multiline: true,
                        minWidth: 300,
                        minHeight: 100
                    },
                    {
                        type: 'textbox',
                        name: 'styles',
                        label: 'Styles',
                        value: '',
                        multiline: true,
                        minWidth: 300,
                        minHeight: 100
                    },
                    {
                        type: 'textbox',
                        name: 'center',
                        label: 'Center Map Location'
                    },
                    {
                        type: 'textbox',
                        name: 'clustergridsize',
                        label: 'Cluster Grid Size',
                        value: '50'
                    },
                    {
                        type: 'listbox', 
                        name: 'scrollwheel', 
                        label: 'Map Scrolling', 
                        'values': [
                            {text: 'Enabled', value: 'true'},
                            {text: 'Disabled', value: 'false'}
                        ]
                    }],
                    onsubmit: function( e ) {
                           
                        var markersInput = e.data.markers.split("\n"), styles = [], markersArr = [], markersSubarr = [], markers = [];
                        
                        //var rx1 = 
                        //styles = styles.replace( /\[/g , '{:' ).replace( /\]/g, ':}' ).replace( /\s+/g , '' );
                        
                        //console.log(styles);
                        
                        for (i = 0; i < markersInput.length; i++) {

                            markersArr[i] = markersInput[i].split(',');
                            //markersSubarr = markersArr[i];
                            
                            for (j = 0; j < markersArr[i].length; j++) {
                                markersArr[i][j] = markersArr[i][j].trim();
                            }
                            
                            markers[i] = "{:'" + markersArr[i][0] + "','" + markersArr[i][1] + "'," + markersArr[i][2] + "," + markersArr[i][3] + ":}";
                            
                        }
                        
                        //markers = "{:" + markers + ":}";  
                        markers = ( e.data.markers.length === 0 ? '' : ' markers=\"{:' + markers + ':}\"' );
                        
                        var zoom = ( e.data.zoom.length === 0 ? '' : ' zoom=\"' + e.data.zoom.trim() + '\"' );
                        var width = ( e.data.width.length === 0 ? '' : ' width=\"' + e.data.width.trim() + '\"' );
                        var height = ( e.data.height.length === 0 ? '' : ' height=\"' + e.data.height.trim() + '\"' );
                        var iconurl = ( e.data.iconurl.length === 0 ? '' : ' iconurl=\"' + e.data.iconurl.trim() + '\"' );
                        var maptype = ( e.data.maptype.length === 0 ? '' : ' maptype=\"' + e.data.maptype.trim() + '\"' );
                        var scrollwheel = ( e.data.scrollwheel.length === 0 ? '' : ' scrollwheel=\"' + e.data.scrollwheel.trim() + '\"' );
                        var center = ( e.data.center.length === 0 ? '' : ' center=\"{:' + e.data.center.trim() + ':}\"' );
                        var clustergridsize = ( e.data.clustergridsize.length === 0 ? '' : ' clustergridsize=\"' + e.data.clustergridsize.trim() + '\"' );
                        var styles = ( e.data.styles.length === 0 ? '' : ' styles=\"' + e.data.styles.trim().replace( /\[/g , '{:' ).replace( /\]/g, ':}' ).replace( /\s+/g , '' ).replace( /\"/g , '\'' ) + '\"' );
                        
                        //console.log(markers);
                        
                        var shortcodeString = '[googlemap' + width + height + iconurl + zoom + maptype + scrollwheel + center + clustergridsize + markers + styles +']';
                            
                        editor.insertContent( shortcodeString );
                    }
                });
            }
        });
    });
})();