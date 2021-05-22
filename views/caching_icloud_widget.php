<div class="col-lg-4 col-md-6">
	<div class="card" id="caching-icloud-widget">
		<div class="card-header" data-container="body">
			<i class="fa fa-cloud"></i>
			    <span data-i18n="caching.widget_icloud_title"></span>
			    <a href="/show/listing/caching/caching" class="pull-right"><i class="fa fa-list"></i></a>
			
		</div>
		<div class="card-body text-center"></div>
	</div><!-- /panel -->
</div><!-- /col -->

<script>
$(document).on('appUpdate', function(e, lang) {

    $.getJSON( appUrl + '/module/caching/caching_icloud_widget', function( data ) {

    	if(data.error){
    		//alert(data.error);
    		return;
    	}

		var panel = $('#caching-icloud-widget div.card-body'),
		baseUrl = appUrl + '/show/listing/caching/caching';
		panel.empty();

		// Set statuses
		if(data.icloud != "0"){
			panel.append(' <a href="'+baseUrl+'" class="btn btn-info"><span class="bigger-150">'+fileSize(data.icloud, 2)+'</span><br>'+i18n.t('caching.iclouddata')+'</a>');
		} else if(data.icloud) {
            panel.append(' <a href="'+baseUrl+'" class="btn btn-info disabled"><span class="bigger-150">'+fileSize(data.icloud, 2)+'</span><br>'+i18n.t('caching.iclouddata')+'</a>');
        }
        
		if(data.itunesu != "0"){
			panel.append(' <a href="'+baseUrl+'" class="btn btn-info"><span class="bigger-150">'+fileSize(data.itunesu, 2)+'</span><br>'+i18n.t('caching.itunesudata')+'</a>');
		} else if(data.itunesu) {
            panel.append(' <a href="'+baseUrl+'" class="btn btn-info disabled"><span class="bigger-150">'+fileSize(data.itunesu, 2)+'</span><br>'+i18n.t('caching.itunesudata')+'</a>');
        }
        
		if(data.other != "0"){
			panel.append(' <a href="'+baseUrl+'" class="btn btn-info"><span class="bigger-150">'+fileSize(data.other, 2)+'</span><br>'+i18n.t('caching.otherdata')+'</a>');
		} else if(data.other) {
            panel.append(' <a href="'+baseUrl+'" class="btn btn-info disabled"><span class="bigger-150">'+fileSize(data.other, 2)+'</span><br>'+i18n.t('caching.otherdata')+'</a>');
        }

    });
});
</script>
