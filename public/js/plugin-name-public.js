
function likeAdsPost( that = false, id = false ) {
  if( ! that || ! id ) {return;}
  jQuery.ajax({
    type: 'post',
    url: siteConfig?.ajaxUrl ?? '',
    dataType: 'json',
    data: {
      action: 'mja_ads_like',
      ad_id: id,
      ajax_nonce: siteConfig?.mja_ads_like_nonce ?? ''
    },
    success: ( data ) => {
      console.log( 'success', data.data.message);
      if( ! data.success ) {alert( data.data );}
      data.data.action = ( data.data.action == 'liked' ) ? 'Unlike' : 'Like';
      if( data.data.action == 'liked' ) {
        jQuery( that ).addClass( 'unfav' );
        jQuery( that ).removeClass( 'fav' );
      }else{
        jQuery( that ).addClass( 'fav' );
        jQuery( that ).removeClass( 'unfav' );
      }
      
      jQuery( that ).children( 'span' ).text( data.data.action );
      if( data.data.totals == 0 ) {
        jQuery( '.activity-like-' + id ).parents( '.activity-state' ).removeClass('has-likes');
        jQuery( '.activity-like-' + id ).text( '' );
      }else{
        jQuery( '.activity-like-' + id ).parents( '.activity-state' ).addClass('has-likes');
        jQuery( '.activity-like-' + id ).text( data.data.totals );
      }
    },
    error: ( err ) => {
      console.log( 'fail', err );
    }
  });
}
function iframe_loaded( that = false, id = false ) {
    if( ! id ) {return;}
    if( ! window.AdsIframeIds ) {window.AdsIframeIds = [];}
    window.AdsIframeIds.push( [that, id] );
    console.log( 'video ads. youtube init...');
    onYouTubeIframeAPIReady();
  }
var visibleY = function(el){
  var rect = el.getBoundingClientRect(), top = rect.top, height = rect.height, 
    el = el.parentNode;
  let body = document.documentElement.clientHeight;
  return ( rect.bottom >= 0 && rect.top <= body );
  do {
    rect = el.getBoundingClientRect();
    if (top <= rect.bottom === false) return false;
    // Check if the element is out of view due to a container scrolling
    if ((top + height) <= rect.top) return false
    el = el.parentNode;
  } while (el != document.body);
  // Check its within the document viewport
  // return ( top <= document.documentElement.clientHeight && rect.bottom >= 0 );
};
function onAdsPlayerReady( e = false ) {
  if( ! e ) {return;}
  var playTimeout;let done = false;
  var video = videojs(e + '' );
  
  // remainingTime() requestFullscreen() src('') video.volume(0.5);
  // volume() for get volume(0.5) for set
  // playing waiting seeking seeked ended play pause

  window.addEventListener( 'scroll', function() {
    if( visibleY( document.getElementById( e ) ) ) {
      if( MJA_ADS_AUTOPLAY && ! done ) {
        video.play();
      }
    }else{
        video.pause();
    }
  }, false );
  var volume_button = document.getElementById( e ).nextElementSibling;
  volume_button.classList.add( 'insight' );
  volume_button.addEventListener("click", function() {
    switch( volume_button.getAttribute('data-toggled') ) {
      case 'on':
        video.muted(false);
        volume_button.setAttribute('data-toggled', 'off');
        volume_button.classList.add( 'bb-icon-volume-up' );
        volume_button.classList.remove( 'bb-icon-volume-mute' );
        break;
      case 'off':
        video.muted(true);
        volume_button.setAttribute('data-toggled', 'on');
        volume_button.classList.add( 'bb-icon-volume-mute' );
        volume_button.classList.remove( 'bb-icon-volume-up' );
        break;
      default:break;
    };
  });

  video.on('timeupdate', function() {
    if( video.currentTime() >= MJA_ADS_TIMING && ! done ) {
      video.pause();videojs.log('Paused');done = true;
    }
  });
  video.on('pause', function() {
    document.getElementById( e ).classList.add( 'visible' );
  });
  video.on('ended', function() {
    document.getElementById( e ).classList.add( 'visible' );
  });
}

function onYouTubeIframeAPIReady() {
  if( ! window.AdsIframeIds ) {window.AdsIframeIds = [];}
  window.AdsIframeIds.forEach((index) => {
    window.MJA_ADS_SOUND = 100;
    var player;var done = did = false;let firstPlay = true;let changedVolume = true;
    player = new YT.Player( index[1], {
      height: '390',
      width: '640',
      videoId: index[0],
      playerVars: {
      'playsinline': 0,
      'autoplay': 0,
      'controls': 0,
      'showinfo': 0,
      'rel': 0,
      // 'endSeconds': 30
      },
      events: {
      // 'onPlayback': function(event) {},
      'onReady': function(event) {
        player.setVolume(window.MJA_ADS_SOUND);player.mute();
        var duration = document.getElementById( index[1] ).parentNode.lastChild;
        duration.setAttribute( 'data-duration', player.getDuration() );
        document.getElementById( index[1] ).parentNode.classList.add( 'ads-video' );
        window.addEventListener( 'scroll', function() {
          // console.log( window.scrollY ); It will help to detect ScrollUp function
          if( visibleY( document.getElementById( index[1] ) ) && MJA_ADS_AUTOPLAY && ! done && ! did ) {
            if( [ 2, -1 ].includes( player.getPlayerState() ) || firstPlay ) {
              event.target.playVideo();
              document.getElementById( index[1] ).parentNode.classList.remove( 'visible' );
              console.log( 'Video Started' );firstPlay = false;
            }
          }else{
            // if( player.getPlayerState() == 1 )
              event.target.pauseVideo();event.target.seekTo(0);
          }
        }, false );
        var pause_button = document.getElementById( index[1] ).parentNode.nextElementSibling;
        pause_button.classList.add( 'insight' );
        pause_button.addEventListener("click", function() {
          switch( pause_button.getAttribute('data-toggled') ) {
            case 'on':
              pause_button.setAttribute('data-toggled', 'off');
              pause_button.classList.add( 'bb-icon-play' );
              pause_button.classList.remove( 'bb-icon-pause' );
              document.getElementById( index[1] ).parentNode.classList.remove( 'visible' );
              player.playVideo();did = false;
              console.log( 'Play function executed' );
              break;
            case 'off':
              pause_button.setAttribute('data-toggled', 'on');
              pause_button.classList.add( 'bb-icon-pause' );
              pause_button.classList.remove( 'bb-icon-play' );
              document.getElementById( index[1] ).parentNode.classList.add( 'visible' );
              player.pauseVideo();did = true;
              console.log( 'Pause function executed' );
              break;
            case 'replay':
              pause_button.setAttribute('data-toggled', 'on');
              pause_button.classList.add( 'bb-icon-play' );
              pause_button.classList.remove( 'bb-icon-pause' );
              pause_button.nextElementSibling.classList.add( 'insight' );
              document.getElementById( index[1] ).parentNode.classList.remove( 'visible' );
              player.playVideo();did = false;
              console.log( 'Replay function executed' );
              break;
            default:break;
          };
        });
        var volume_button = pause_button.nextElementSibling;
        volume_button.classList.add( 'insight' );
        volume_button.addEventListener("click", function() {
          changedVolume = true;
          switch( volume_button.getAttribute('data-toggled') ) {
            case 'on':
              window.MJA_ADS_SOUND = 100;
              player.unMute();
              volume_button.setAttribute('data-toggled', 'off');
              volume_button.classList.add( 'bb-icon-volume-up' );
              volume_button.classList.remove( 'bb-icon-volume-mute' );
              break;
            case 'off':
              window.MJA_ADS_SOUND = 0;
              player.mute();
              volume_button.setAttribute('data-toggled', 'on');
              volume_button.classList.add( 'bb-icon-volume-mute' );
              volume_button.classList.remove( 'bb-icon-volume-up' );
              break;
            default:break;
          };
          console.log( 'Volume function executed' );
        });
      },
      'onStateChange': function(event) {
        if( event.data == YT.PlayerState.PLAYING ){
          if( ! visibleY( document.getElementById( index[1] ) ) && !done ) {
            document.getElementById( index[1] ).parentNode.classList.add( 'visible' );
            event.target.pauseVideo();
            console.log( 'Video Paused' );
          }
          if( player.getCurrentTime() >= MJA_ADS_TIMING ){
            document.getElementById( index[1] ).parentNode.classList.add( 'visible' );
            player.stopVideo();done = true;// player.seekTo(0);

            pause_button = document.getElementById( index[1] ).parentNode.nextElementSibling;
            pause_button.setAttribute('data-toggled', 'replay');
            pause_button.classList.add( 'bb-icon-rotate-cw' );
            pause_button.classList.remove( 'bb-icon-pause', 'bb-icon-play' );

            pause_button.nextElementSibling.classList.remove( 'insight' );
            document.getElementById( index[1] ).parentNode.classList.add( 'visible' );
            clearInterval(runtimeVideo);
          }
          if( changedVolume ) {
            var volume_button = document.getElementById( index[1] ).parentNode.nextElementSibling.nextElementSibling;changedVolume = false;
            if( window.MJA_ADS_SOUND != 0 && player.isMuted() ){
              player.unMute();
              volume_button.setAttribute('data-toggled', 'off');
              volume_button.classList.add( 'bb-icon-volume-up' );
              volume_button.classList.remove( 'bb-icon-volume-mute' );
            }else{
              player.mute();
              volume_button.setAttribute('data-toggled', 'on');
              volume_button.classList.add( 'bb-icon-volume-mute' );
              volume_button.classList.remove( 'bb-icon-volume-up' );
            }
          }
        }
        if( event.data == YT.PlayerState.BUFFERING ){}
        if( event.data == YT.PlayerState.CUED ){}
        if( event.data == YT.PlayerState.ENDED ){
          console.log( 'video Ended!' );
          document.getElementById( index[1] ).parentNode.classList.add( 'visible' );
          pause_button = document.getElementById( index[1] ).parentNode.nextElementSibling;
          pause_button.setAttribute('data-toggled', 'replay');
          pause_button.classList.add( 'bb-icon-rotate-cw' );
          pause_button.classList.remove( 'bb-icon-pause' );
        }
        if( event.data == YT.PlayerState.PAUSED ){
          console.log( index[1] + 'video Paused' );
        }
      }
      }
    });
  });
  window.AdsIframeIds = [];
}

function continue_javascript_on_iframe( that = false, id = false ) {
  console.log( 'Ads Function is in processing...' );
  // alert( jQuery( that ).attr('src') );
  let iframe = document.getElementById( id ).contentWindow;
  let doc = iframe.document;
  // var elem = document.getElementById("player");doc.body.removeChild( elem );
  var css = '<style>.ytp-chrome-top.ytp-show-cards-title {display: none;}a.ytp-watermark.yt-uix-sessionlink {display: none;}.html5-endscreen.ytp-player-content.videowall-endscreen {display: none;}</style>';
  doc.head.innerHTML += css;
  // doc.head.innerHTML = doc.head.innerHTML + css;
  // let link = document.createElement("link");link.href = "have_a-style.css";link.rel = "stylesheet";link.type = "text/css";doc.head.appendChild(link);
  console.log( 'Ads Function in Executed' );
}
function maj_ads_class_exists(cls) {
  // Checks if the class exists  
  // 
  // version: 902.1018
  // discuss at: http://phpjs.org/functions/class_exists
  // +   original by: Brett Zamir
  // *     example 1: function class_a() {this.meth1 = function() {return true;}};
  // *     example 1: var instance_a = new class_a();
  // *     example 1: class_exists('class_a');
  // *     returns 1: true
  var i = '';
  cls = window[cls]; // Note: will prevent inner classes

  if (typeof cls !== 'function') {return false;}

  for (i in cls.prototype) {
      return true;
  }
  for (i in cls) { // If static members exist, then consider a "class"
      if (i !== 'prototype') {
          return true;
      }
  }
  if (cls.toSource && cls.toSource().match(/this\./)) { 
      // Hackish and non-standard but can probably detect if setting
      // a property (we don't want to test by instantiating as that
      // may have side-effects)
      return true;
  }
  
  return false;
}
function mja_ads_what_happend( that = false ) {
  if( ! that ) {return false;}
  var  duration = that.getAttribute( 'data-duration' );
  if( typeof duration === 'underfine' || duration === 0 ) {duration=120;}
  duration = ( duration / 2 );
  var url = that.getAttribute('data-href');
  var csSheet = '<style id="mja_ads_youtube_pogressbar_style">\
  body:after, #masthead-container.ytd-app:after {\
    content: "";\
    position: absolute;\
    top: 0;\
    left: 0;\
    height: 5px;\
    width: 100%;\
    background: yellow;\
    transition: ' + duration + 's ease all;\
  }\
  body.playing:after,#masthead-container.ytd-app.playing:after {width: 100%;}</style>';
  var newWin = window.open( url, 'mjaadswindow', '', '_blank' );
  if( ! newWin ){console.log('Window opening failed');return false;}
  if ( window.focus ) {newWin.focus()}
  // newWin.document.open();
  newWin.document.head.innerHTML = newWin.document.head.innerHTML + csSheet;
	// newWin.document.write(csSheet);
	// newWin.document.close();
  // newWin.onload = function (newWin) {
  //   newWin.console.log("...something...");
  // }
  // setInterval(() => {
  //   // document.head.innerHTML += csSheet;
  //   newWin.document.getElementsByTagName('body').innerHTML += csSheet;
  // }, 1000);
  // console.log( csSheet );
  // getElementsByTagName('head')[0].appendChild


  // Submitting Event
  // jQuery.ajax({
  //   type: 'post',
  //   url: siteConfig?.ajaxUrl ?? '',
  //   dataType: 'json',
  //   data: {
  //     action: 'mja_ads_click',
  //     ad_id: 'id',
  //     ajax_nonce: siteConfig?.mja_ads_click_nonce ?? ''
  //   },
  //   success: ( data ) => {
  //     console.log( 'success', data.data.message);
  //     if( ! data.success ) {alert( data.data );}
  //   },
  //   error: ( err ) => {
  //     console.log( 'fail', err );
  //   }
  // });
  return false;
}