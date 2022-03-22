
// 一覧ページのアンカーリンク調整
function setPagelink( _headerHight ) {
	var headerHight = _headerHight;

	/* outpagelink */
	var hash = location.hash;

	if ( hash !== '' ) {
		var idName = hash.substr( 1 ); // １文字目（ # ）は切り取る

		if ( $( '#' + idName ).length > 0 ) {
			// var position = $( '#' + idName ).position().top;
			var position = document.getElementById( idName ).offsetTop;
			$( ".list-data tbody" ).animate( { scrollTop: position - 51 }, 5, "swing" );
		}

	}
}

$( function () {
	setPagelink( $( 'header' ).height() );//ヘッダーの高さを入れる
} )
