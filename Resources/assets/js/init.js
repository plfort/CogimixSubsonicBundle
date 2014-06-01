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
	
	
	$modal.on('click','.subsonicItem',function(event){
		$modal.find('li').removeClass('active');
		$(this).closest('li').addClass('active');
		$.get(Routing.generate('_subsonic_edit',{'id':$(this).data('id')}),function(response){
			if(response.success==true){
				var formContainer=$modalBody.find('div#subsonicServerInfoForm')
				formContainer.html(response.data.formHtml);
				//$("#manageCustomProviderModal").modal('toggle');
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
				currentForm.find('.formMessage').html('<div class="span3 center alert alert-success">Saved !</div>')
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
});