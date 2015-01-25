$(document).ready(function(){
	var $modal = $("#manageSubsonicServerModal");
	var $modalBody = $("#manageSubsonicServerModal .modal-body");
	$("#manageSubsonicServerBtn").click(function(event){
		$.get(Routing.generate('_subsonic_manage_modal'),function(response){
			if(response.success==true){
				$modalBody.html(response.data.modalContent);
				$modal.modal('toggle');
			}
		},'json');
		
	});
	
	
	$modal.on('click','.subsonicServerItem',function(event){
		$modal.find('li').removeClass('active');
		$(this).closest('li').addClass('active');
		$.get(Routing.generate('_subsonic_edit',{'id':$(this).data('id')}),function(response){
			if(response.success==true){
				var formContainer=$modalBody.find('div#subsonicServerInfoForm')
				formContainer.html(response.data.formHtml);
				
			}
		},'json');
	});
	
	$modalBody.on('click','#createSubsonicServerInfoBtn',function(event){
		$.get(Routing.generate('_subsonic_create'),function(response){
			if(response.success==true){
				var formContainer=$modalBody.find('div#subsonicServerInfoForm')
				formContainer.html(response.data.formHtml);
				//$("#manageCustomProviderModal").modal('toggle');
			}
		},'json');
		
		return false;
		
	});
	
	$modalBody.on('click','.removeSubsonicServerInfoBtn',function(event){
		$.get($(this).attr('href'),function(response){
			if(response.success==true){
				$modalBody.find("#subsonicServerInfoList a[data-id='"+response.data.id+"']").parent().remove();
				$modalBody.find("#subsonicServerInfoForm").empty();
				$modal.addClass('refreshOnClose');
			}
		},'json');
		
		return false;
		
	});
	
	
	$modalBody.on('submit','form',function(event){
		var formData = $(this).closest('form').serialize();
		var currentForm = $(this);
		$.post($(this).attr('action'),formData,function(response){
			currentForm.find('.formMessage').empty();
			if(response.success==true){
				$modal.addClass('refreshOnClose');
				currentForm.find('.formMessage').html('<div class="col-sm-12 center alert alert-success">Saved !</div>')
				if(response.data.formType=='create'){
					$modalBody.find("#subsonicServerInfoList").append(response.data.newItem);
				    
				}
		
			}else{
				var formContainer=$modalBody.find('div#subsonicServerInfoForm')
				formContainer.html(response.data.formHtml);
			}
		},'json');
		
		return false;
	});
	
$modalBody.on('click','.testSubsonicServerBtn',function(event){
		
		var formData = $(this).closest('form').serialize();
		$.post(Routing.generate('_subsonicserver_test'),formData,function(response){
			$modalBody.children('.testSubsonicServerResult').empty();
			if(response.success==true){
				$modalBody.find('.testSubsonicServerResult').html('<div class="alert alert-success">'+response.data.message+'</div>');
			}else{
				$modalBody.find('.testSubsonicServerResult').html('<div class="alert alert-danger">'+response.data.message+'</div>');
			}
			
		},'json');
		return false;
	});

$("#playlist-container").on('click','.showPlaylistSubsonicBtn',function(event){
	var playlistElement = $(this).closest('.subsonic-playlist-item');
	var playlistName = $(this).html();
	
	$.get(Routing.generate('_subsonic_playlist_songs',{'serverId':playlistElement.data('serverid'), 'playlistId':playlistElement.data('id')}),function(response){
		if(response.success == true){
			$pane.playlist.content.html(render('playlistExternalDetailSkeletonTpl',{'playlist':{name:playlistName},'user':currentUser}));
			renderResult(response.data.tracks,{tpl:'trackNoSortTpl',tabName:playlistName,alias:'playlist-content'});
        	$("#wrap").animate({scrollTop:0});

		}
	},'json');
	return false;
});

$("#playlist-container").on('click','.playPlaylistSubsonicBtn',function(event){
	var playlistElement = $(this).closest('.subsonic-playlist-item');
	$.get(Routing.generate('_subsonic_playlist_songs',{'serverId':playlistElement.data('serverid'), 'playlistId':playlistElement.data('id')}),function(response){
		if(response.success == true){
			musicPlayer.removeAllSongs();
			musicPlayer.addSongs(response.data.tracks);
            musicPlayer.play();
		}
	},'json');
	return false;
});


});

droppedHookArray['subsonic-playlist'] = function(droppedItem,callback){
	var playlistId=droppedItem.data('id');
	var serverId=droppedItem.data('serverid');
	$.get(Routing.generate('_subsonic_playlist_songs',{'serverId':serverId, 'playlistId':playlistId}),function(response){
        if(response.success==true){
            logger.debug(response.data.tracks);
            callback(response.data.tracks);
            }
        },'json');

}