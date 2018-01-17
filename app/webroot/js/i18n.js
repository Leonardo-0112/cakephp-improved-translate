// Language selector
(function() {
	$(function() {

		var $link = $(".js-choose-language");

		if( ! $link.length ) return;

		$link.click(function( evt ) {

			evt.preventDefault();

			$.ajax({
				url: $(this).attr("href"),
				dataType: "json"
			})
			.done(function( resp ) {
				if( resp.status == "OK" ) {
					window.location.reload();
				}
			});
		});
	});
})();
