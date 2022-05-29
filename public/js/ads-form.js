

( function ( $, document, window ) {
  
	class AdsRegFrontEnd {
		constructor() {

      this.ajaxUrl = siteConfig?.ajaxUrl ?? '';
      this.adsCosts = adsInformation?.adsCosts ?? [];
			console.log(this.adsCosts);
      this.init();
			this.submit();
		}
		init(){
			var that = this;
			$('input[name=mjc_ads_inf_location], select[name=mjc_ads_inf_duration]').on( 'change', function(e) {
				var price = 0.00,
						loc = ( $('input[name=mjc_ads_inf_location][value=featured]').prop('checked') ) ? 'featured' : 'activity',
						dur = $('select[name=mjc_ads_inf_duration][selected]').children("option:selected").val();
				var dur = document.getElementById("mjc_ads_inf_duration").selectedOptions[0].value;
				if( dur == '' ){return;}
				var index = loc + '_' + dur;
				switch( index ) {
					case 'activity_one_week':
						price = that.adsCosts.activity_1_week;
						break;
					case 'activity_two_week':
					case 'activity_two_weeks':
						price = that.adsCosts.activity_2_week;
						break;
					case 'activity_one_month':
						price = that.adsCosts.activity_1_month;
						break;
					case 'featured_one_week':
						price = that.adsCosts.featured_1_week;
						break;
					case 'featured_two_week':
					case 'featured_two_weeks':
						price = that.adsCosts.featured_2_week;
						break;
					case 'featured_one_month':
						price = that.adsCosts.featured_1_month;
						break;
					default:
						// if( navigator ){navigator.clipboard.writeText( index );}
						price = 0.00;
						break;
				};
				console.log( index, price );
				$('#mjc_ads_price_tr .price_table > .suffix, #mjc_ads_price_tr .price_table > .prefix').text('');
				$('#mjc_ads_price_tr .price_table > .price').text( price );
				switch( that.adsCosts.currency ){
					case 'dollars':
						$('#mjc_ads_price_tr .price_table > .prefix').text( '$' );
						break;
					case 'pounds':
						$('#mjc_ads_price_tr .price_table > .prefix').text( '£' );
						break;
					case 'cfa':
					case 'xaf':
						$('#mjc_ads_price_tr .price_table > .prefix').text( that.adsCosts.currency.toUpperCase() );
						break;
					case 'ngn':
						$('#mjc_ads_price_tr .price_table > .prefix').text( '₦' );
						break;
					case 'euro':
						$('#mjc_ads_price_tr .price_table > .suffix').text( '€' );
						break;
					default:
						$('#mjc_ads_price_tr .price_table > .prefix').text( that.adsCosts.currency.toUpperCase() );
						break;
				};
			})
		}
		submit() {
			$(document).ready(function (e) {
				$('#imageUploadForm').on('submit',(function(e) {
						e.preventDefault();
						var formData = new FormData(this);
		
						$.ajax({
								type:'POST',
								url: $(this).attr('action'),
								data:formData,
								cache:false,
								contentType: false,
								processData: false,
								success:function(data){
										console.log("success");
										console.log(data);
								},
								error: function(data){
										console.log("error");
										console.log(data);
								}
						});
				}));
		
				$("#ImageBrowse").on("change", function() {
						$("#imageUploadForm").submit();
				});
			});
		}
	}
	new AdsRegFrontEnd();
} )( jQuery, document, window );
