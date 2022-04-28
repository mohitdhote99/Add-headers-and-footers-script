(function($){
//  setTimeout is to run codemirror if any file load before codemirror root files
setTimeout(ahafs_codemirr,100);

	function ahafs_codemirr(){

	// function used for toaster behaviour
		function toasterv(msg){
			$('.toaster_view').addClass('active');
			$('.toaster_view p').html(msg);
			setTimeout(()=>{
				$('.toaster_view p').html('');
				$('.toaster_view').removeClass('active');
				$('.toaster_view').attr('data-ahafsresult','');
			},2000);
		}

	// get data of the text area from DOM and assign to codemirror for visiblity 
		var my_code_head = $('.Editor_header');
		var my_code_foot = $('.Editor_footer');
		var my_code_body = $('.Editor_body');
		
		var editor_header = CodeMirror.fromTextArea(my_code_head[0],{
						mode:'javascript',
						lineNumbers:true,
						lineWrapping:true,
						autocorrect:true
					});

		var editor_body = CodeMirror.fromTextArea(my_code_body[0],{
						mode:'javascript',
						lineNumbers:true,
						lineWrapping:true,
						autocorrect:true
					});


		var editor_footer = CodeMirror.fromTextArea(my_code_foot[0],{
						mode:'javascript',
						lineNumbers:true,
						lineWrapping:true,
						autocorrect:true
			});

// event used for interaction with php for submitting the data
		$(document).on('click','.ahafs_save_code',function(e){
			e.preventDefault();
			let gethead = editor_header.getValue();
			let getbody = editor_body.getValue();
			let getfoot = editor_footer.getValue();
				let sendData = {editor_header:gethead,editor_body:getbody,editor_footer:getfoot};
				$.ajax({
					method:'post',
					data:{ahafs_codemirror:sendData,action:'ahafsAjax_action'},
					url:ahafsURL_src.ahafs_url+'?page=ahafs_page',
					dataType:'json',
					success:function(response){
						$.each(response,(_key,_val)=>{
							if (_key == 'error' && _val == 'updated') {
								$('.toaster_view').attr('data-ahafsresult','success');
								toasterv('blank updated');
							}else{
								$('.toaster_view').attr('data-ahafsresult',_key);
								toasterv(_val);
							}
						});
					},
					error:function(xhr){
						console.log(xhr.responseText);
					}
				});
		});

	}


// main ahafs function closing
})(jQuery);