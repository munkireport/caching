	<div class="col-lg-4 col-md-6">
	<div class="card" id="caching-reachable-servers-widget">
		<div class="card-header" data-container="body" >
			<i class="fa fa-hand-lizard-o"></i>
			    <span data-i18n="caching.widget_reachable_servers"></span>
			    <a href="/show/listing/caching/caching" class="pull-right"><i class="fa fa-list"></i></a>
			
		</div>
		<div class="list-group scroll-box"></div>
	</div><!-- /panel -->
</div><!-- /col -->

<script>
$(document).on('appUpdate', function(e, lang) {
	
	var box = $('#caching-reachable-servers-widget div.scroll-box');
	
	$.getJSON( appUrl + '/module/caching/get_reachable_cache_name', function( data ) {
		
		box.empty();
		if(data.length){
			$.each(data, function(i,d){
				var badge = '<span class="badge badge-secondary pull-right">'+d.count+'</span>';
                box.append('<a href="'+appUrl+'/show/listing/caching/caching/#'+d.reachability+'" class="list-group-item list-group-item-action">'+d.reachability+badge+'</a>')
			});
		}
		else{
			box.append('<span class="list-group-item">'+i18n.t('caching.noservers')+'</span>');
		}
	});
});	
</script>
