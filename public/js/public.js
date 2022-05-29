

( function ( $, document, window ) {
  
	class Clock {
		constructor() {

      this.ajaxUrl = siteConfig?.ajaxUrl ?? '';
			this.initializeRadio();
      this.copy();
      this.imgSelectAfter();
      this.status_toggle();
      this.changeVideoThumbs();
      this.changeImageThumbs();
      // this.initializeActionBtn();
		}
		initializeActionBtn() {
      $('.mja_ads_action_btn').on( 'click', function(e) {
        if( e.target ) {
          console.log( 'Initializing Actions' );
        }else{
          console.log(e);
        }
      });
    }
		initializeRadio() {
      var that = this;
      $('[name=mjc_ads_inf_type]').on( 'change click', function(e) {
        that.fetchingAds_Type( e );
      });
      $('[name=mjc_ads_param_type_media]').on( 'change click', function(e) {
        that.fetchingMedia_type( e );
      });
      $( window ).on( 'load', function() {
        that.fetchingRadio( '[name=mjc_ads_inf_type]' );
        that.fetchingRadio( '[name=mjc_ads_param_type_media]' );
      });
    }
    fetchingRadio( e ) {
      var that = this;
      var bool = $( e ).attr('checked');
      if( typeof bool !== undefined && bool !== false  ){
        $( e + '[checked]' ).click();
        
      }else{
        $( e + ':first-child' ).click();
      }
      that.fetchingAds_Type({target: '[name=mjc_ads_param_type_media]'});
      that.fetchingMedia_type({target: '[name=mjc_ads_param_type_media]'});
    }
		fetchingAds_Type( e = [] ) {
      var tab = [
        '#mjc_ads_param_type_text, #mjc_ads_param_type_allowshortcode',
        '#mjc_ads_param_type_rich_text, #mjc_ads_param_type_rich_fontsize, #mjc_ads_param_type_rich_bgcolor, #mjc_ads_param_type_rich_txtcolor',
        'input[name=mjc_ads_param_type_media]',
        'input[name=mjc_ads_layout], input[name=mjc_ads_layout_not], #mjc_ads_layout_container_id, #mjc_ads_layout_container_class, #mjc_ads_dc_type, #mjc_ads_dc_position, #mjc_ads_dc_injposition, #mjc_ads_dc_repeatposition, #mjc_ads_2whom_vstype, #mjc_ads_2whom_dvtype'
      ];
      switch( e.target && $(e.target).attr('value') ) {
        case 'plaintext' :
          $( '.mujah_meta_box.mjc_ads_inf_type_meta_box' ).addClass( 'plaintext' ).removeClass( 'richtext image video' );
          break;
        case 'richtext' :
          $( '.mujah_meta_box.mjc_ads_inf_type_meta_box' ).addClass( 'richtext' ).removeClass( 'plaintext image video' );
          break;
        case 'image' :
          $( '.mujah_meta_box.mjc_ads_inf_type_meta_box' ).addClass( 'image' ).removeClass( 'richtext plaintext video' );
          break;
        case 'video' :
          $( '.mujah_meta_box.mjc_ads_inf_type_meta_box' ).addClass( 'video' ).removeClass( 'richtext plaintext image' );
          break;
        default :
          break;
      }
			// setInterval( () => this.time(), 1000 );
		}
		fetchingMedia_type( e = [] ) {
      var media = [
        '#mjc_ads_param_type_media, #mjc_ads_param_type_media_prev, #mjc_ads_param_type_media_img, #mjc_ads_param_type_media_url, #mjc_ads_param_type_media_width, #mjc_ads_param_type_media_height',
        '#mjc_ads_param_type_media_video'
      ];
      switch( e.target && $(e.target).attr('value') ) {
        case 'image' :
          $( media[0] ).parents('tr').css( 'display', 'block' );
          $( media[1] ).parents('tr').css( 'display', 'none' );
          break;
        case 'video' :
          $( media[1] ).parents('tr').css( 'display', 'block' );
          $( media[0] ).parents('tr').css( 'display', 'none' );
          break;
        default :
          break;
      }
			// setInterval( () => this.time(), 1000 );
		}
    copy() {
      $(document).on('click', "#mjc_ads_short_code, .type-visual-ads .mjc_ads_short_code", function (e) {
        e.preventDefault();
        var copyText = ( $(this).data('code') ) ? $(this).data('code') : $(this).text();
        document.addEventListener('copy', function (e) {
          e.clipboardData.setData('text/plain', copyText);
          e.preventDefault();
        }, true);
        document.execCommand('copy');
        var tooltip = $(this).next();
        tooltip.removeClass('shortcode-text-hide');
        setTimeout(function () {
          tooltip.addClass('shortcode-text-hide');
        }, 500);
      });
    }
    imgSelectAfter() {
      $(document).on(
        'click',
        '#mjc_ads_param_type_media_img',
        function (e) {
  
          e.preventDefault()
  
          var button = $(this)
          // WP 3.5+ uploader
          var file_frame
          window.formfield = ''
          // If the media frame already exists, reopen it.
          if (file_frame) {
            // file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
            file_frame.open()
            return
          }
  
          // Create the media frame.
          file_frame = wp.media.frames.file_frame = wp.media({
            id: 'mja_type_image_wp_media',
            title: 'Select / Upload Image.',
            button: {
              text: 'Insert this media'
            },
            library: {
              type: 'image' // audio, video
            },
            multiple: false
          })
  
          file_frame.on('select', function () {
  
            var selection = file_frame.state().get('selection')
            selection.each(function (attachment, index) {
              attachment = attachment.toJSON()
              if (0 === index) {
               console.log( attachment );
                // place first attachment in field
                $('input[type=hidden][name=mjc_ads_param_type_media_image]').attr( 'value', attachment.url );
                // $('#mjc_ads_param_type_media_prev_ID').val( attachment.id );
                $('#mjc_ads_param_type_media_width').val(attachment.width);
                $('#mjc_ads_param_type_media_height').val(attachment.height);
                // update image preview
                var new_image = '<img width="' + attachment.width + '" height="' + attachment.height + '" title="' + attachment.title + '" alt="' + attachment.alt + '" src="' + attachment.url + '"/>';
                $('#mjc_ads_param_type_media_prev_image').html(new_image);
                $('#mjc_ads_param_type_media_prev_image').removeClass( 'd-none' );
                // $('#wb-ads-image-edit-link').attr('href', attachment.editLink);
              }
            })
          })
  
          // Finally, open the modal
          file_frame.open()
        }
      );
    }
    status_toggle() {
      $('.mjc_ads_short_code_enable .ad_status_toggle_checkbox').on( 'change', function(e) {
        console.log(e);
      });
      $('.mjc_ads_inf_status > select').on( 'change', function(e) {
        console.log(e);
      });
      $('input[name=mjc_ads_inf_location]').on( 'change click', function() {
        if( $('input[name=mjc_ads_inf_location][value=featured]').prop('checked') ) {
          $('#mjc_ads_inf_whatsapp_tr').css( 'display', 'contents' );
        }else{
          $('#mjc_ads_inf_whatsapp_tr').css( 'display', 'none' );
        }
      });
      $('input[name=mjc_ads_inf_location][checked]').click();
    }
    changeVideoThumbs() {
      $( '#mjc_ads_param_type_media_video' ).on('input', function(){ // change 
        if( $( this ).val() == '' ){return;}
        $( '.video-preview_image' ).attr( 'src', $( '.video-preview_image' ).data( 'loader' ) );
        $.ajax({
          type: 'post',
          url: siteConfig?.ajaxUrl ?? '',
          dataType: 'json',
          data: {
            action: 'mja_ads_video_thumb',
            url: $( this ).val(),
            ajax_nonce: siteConfig?.mja_ads_video_thumb_nonce ?? ''
          },
          success: ( res ) => {
            console.log( 'success', res.data );
            if( ! res.success ) {alert( res.data );}
            else{
              $( '.video-preview_image' ).attr( {
                src: res.data.poster,
                alt: res.data.type
              } );
            }
          },
          error: ( err ) => {
            console.log( 'fail', err );
          }
        });
      });
    }
    changeImageThumbs() {
      $('#mjc_ads_param_type_media_img').change(function(){
        var input = this;
        if(input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
            console.log('change detected', e);
            jQuery('#mjc_ads_param_type_media_prev_image img').attr('src', e.target.result);
          }
          reader.readAsDataURL(input.files[0]);
        }
      });
    }
    submitForm() {
      var fcnt = $('#filecount').val();
      var fname = $('#filename').val();
      var imgclean = $('#file');
      if(fcnt<=5){
      data = new FormData();
      data.append('file', $('#file')[0].files[0]);
      var imgname  =  $('input[type=file]').val();
       var size  =  $('#file')[0].files[0].size;
      var ext =  imgname.substr( (imgname.lastIndexOf('.') +1) );
      if(ext=='jpg' || ext=='jpeg' || ext=='png' || ext=='gif' || ext=='PNG' || ext=='JPG' || ext=='JPEG'){
       if(size<=1000000){
          $.ajax({
            url: "<?php echo base_url() ?>/upload.php",
            type: "POST",
            data: data,
            enctype: 'multipart/form-data',
            processData: false,  // tell jQuery not to process the data
            contentType: false   // tell jQuery not to set contentType
          }).done(function(data) {
         if(data!='FILE_SIZE_ERROR' || data!='FILE_TYPE_ERROR' ){
          fcnt = parseInt(fcnt)+1;
          $('#filecount').val(fcnt);
          var img = '<div class="dialog" id ="img_'+fcnt+'" ><img src="<?php echo base_url() ?>/local_cdn/'+data+'"><a href="#" id="rmv_'+fcnt+'" onclick="return removeit('+fcnt+')" class="close-classic"></a></div><input type="hidden" id="name_'+fcnt+'" value="'+data+'">';
          $('#prv').append(img);
          if(fname!=='')
          {
            fname = fname+','+data;
          }else
          {
            fname = data;
          }
           $('#filename').val(fname);
            imgclean.replaceWith( imgclean = imgclean.clone( true ) );
         }
         else
         {
           imgclean.replaceWith( imgclean = imgclean.clone( true ) );
           alert('SORRY SIZE AND TYPE ISSUE');
         }
  
      });
      return false;
      }//end size
      else
      {
          imgclean.replaceWith( imgclean = imgclean.clone( true ) );//Its for reset the value of file type
        alert('Sorry File size exceeding from 1 Mb');
      }
      }//end FILETYPE
      else
      {
        imgclean.replaceWith( imgclean = imgclean.clone( true ) );
        alert('Sorry Only you can uplaod JPEG|JPG|PNG|GIF file type ');
      }
      }//end filecount
      else
      {    imgclean.replaceWith( imgclean = imgclean.clone( true ) );
        alert('You Can not Upload more than 6 Photos');
      }
    }
    disabledField() {
      document.querySelectorAll('[name=input_25]').forEach( e  => {
        e.remove;
      });
    }
	}
	new Clock();
} )( jQuery, document, window );
