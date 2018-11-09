jQuery( function ( $ ) {

    // Sets the outer editor height to be same as the editor contents height.

    function mnltr_cpt_fix_editor_height ( editor ) {

        var $iframe = $( editor.iframeElement );

        if ( $iframe.length == 0 ) {
            return;
        }

        var $body = $( $iframe.get( 0 ).contentDocument.documentElement );
        var height = $body.outerHeight( true );
        
        $iframe.css( 'height', height + 'px' );

    }



    // Do nothing on pages without TinyMCE editors.

    if ( typeof tinymce == 'undefined' ) {
        return;
    }



    // Fix editor height when it is initialised and when its contents change.

    var editors = [];

    tinymce.on( 'SetupEditor', function ( event ) {

        var editor = event.editor;

        if ( editor.id.indexOf( 'acf-editor-' ) < 0 ) {
            return;
        }

        editors.push( editor );

        editor.on( 'init', function ( event ) {
            mnltr_cpt_fix_editor_height( editor );
        });

        editor.on( 'change', function ( event ) {
            mnltr_cpt_fix_editor_height( editor );
        });

    });



    // Also fix editor height on window resize.

    $( window ).on( 'resize', function () {
    
        for ( var k=0, length=editors.length; k<length; k++ ) {
            mnltr_cpt_fix_editor_height( editors[k] );
        }
        
    });
    
});