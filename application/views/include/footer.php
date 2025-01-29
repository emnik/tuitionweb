    <footer class="footer">
        <div class="container">
			<div class="row">
				<div class="col-lg-9 col-sm-8 col-xs-12 pull-left">
					<small>
						<i class="icon-code"></i> 
						Designed and build by <a href="mailto:nikiforakis.m@gmail.com">Nikiforakis Manos</a><br>
						<!-- Build with <a href="http://ellislab.com/codeigniter">CodeIgniter</a> 
						and <a href="http://getbootstrap.com/">Bootstrap</a>. -->
						Icons by <a href="https://fontawesome.com/v3.2.1/icons/">
						<i class="icon-flag"> </i>Font Awesome Icons</a>
					</small>
				</div>
				<div class="col-lg-3 col-sm-4 col-xs-12">
					<div class="social">
						<a id="facebookurl" href="#"><span><i class="icon-facebook-sign icon-2x"></i> facebook</span></a>
						<a id="twitterurl" href="#"><span><i class="icon-twitter-sign icon-2x"></i> twitter</span></a>
					</div>
				</div>			
			</div>
		</div>
	</footer>

	<a class="scrollup" href="#">Scroll</a>



<?php if(!empty($regs) and $regs===true):?>

<!-- LOCAL select 2 (older version) -->
<!-- <link href="<?php //echo base_url('assets/select2/select2.css')?>" rel="stylesheet">
<link href="<?php // echo base_url('assets/select2/select2-bootstrap.css')?>" rel="stylesheet">
<script src="<?php //echo base_url('assets/select2/select2.js')?>"></script>
<script src="<?php //echo base_url('assets/select2/select2_locale_el.js')?>"></script> -->

<!-- CDN for select2 Newer Version -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	

<script type="text/javascript">

$(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '39')) { //ctrl + right arrow
            event.preventDefault();
            $('#footerModal').modal();
        }
    });


$(document).ready(function(){
	// $('#resubscribebtn').removeClass('enabled');
	// $('#resubscribebtn').addClass('disabled');
	$('#resubscribebtn').attr('disabled', 'disabled');
	
		$.ajax({
			url:"<?php echo base_url()?>welcome/social_media",
			dataType: 'json',
			success: function(data, page){
				if(data.facebookurl!=null){
					$('#facebookurl').attr("href", data.facebookurl);
				}
				if(data.twitterurl!=null){
					$('#twitterurl').attr("href", data.twitterurl);
				}
			}
		});


	$('#selectbox').select2({
	dropdownParent: $('#footerModal'),
	width: 'resolve',
    minimumInputLength: 2,
    ajax: {
        url: "<?php echo base_url('welcome/user_list'); ?>",
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return { q: params.term };
        },
        processResults: function(data) {
            console.log(data);  // Inspect the returned JSON

            return {
                results: data  // Use the grouped structure directly
            };
        },
        cache: true
    },
    placeholder: 'όνομα / επώνυμο / τηλέφωνο / τελευταία ψηφία τηλεφώνου',
    allowClear: false
});

	// Manually set the selected value when an option is chosen
	$('#selectbox').on('select2:select', function (e) {
    	// Manually set and trigger change
		var data = e.params.data;
    	console.log('Selected:', data);
		$(e.currentTarget).find("option[value='" + data.id + "']").attr('selected','selected');
	});

	   $('#footerModal').on('shown.bs.modal', function(){
			$('#selectbox').select2("open");
	   });

	   $('#selectbox').on('change', function(){
			$(".alert").hide();
			$('.alert > p > span').remove();
			$('.alert > p > a').remove();
			id=$(this).val();
			if (id!=""){
				$('#resubscribebtn').removeAttr('disabled');
			}
	   })

	   
    // Fetch themes using AJAX
    $.ajax({
        url: '<?php echo base_url('theme/get_themes'); ?>',  // Adjust the URL based on your controller
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // Empty the dropdown before appending new items
            $('#theme-dropdown').empty();
            
            // Loop through the response and add each theme to the dropdown
            $.each(data, function(index, theme) {
                $('#theme-dropdown').append('<li><a href="#" class="theme-option" data-theme-id="'+theme.id+'">'+theme.name+'</a></li>');
            });
        },
        error: function() {
            alert('Error loading themes.');
        }
    });
    
    // When a theme is selected
    $(document).on('click', '.theme-option', function() {
        var themeId = $(this).data('theme-id');

        // Update the theme for the user (via AJAX or form submission)
        $.ajax({
            url: '<?php echo base_url('theme/set_theme'); ?>',  // Adjust the URL
            method: 'POST',
            data: { theme_id: themeId },
			dataType: 'json',
            success: function(response) {
                if (response.success) {
                   location.reload();  // reload the page to apply the theme
               } else {
                    alert('Failed to update theme.');
                }
            }
        });
    });
}); //end of $(document).ready


function fastgo(section){
	id=$('#selectbox').val();
	if (id!=""){
		switch(section)
		{
		case 'card':
		  window.location.href = '<?php echo base_url();?>student/card/'+id;
		  break;
		case 'contact':
		  window.location.href = '<?php echo base_url();?>student/card/'+id+'/contact';
		  break;
		case 'attendance':
		  window.location.href = '<?php echo base_url();?>student/card/'+id+'/attendance';
		  break;
		case 'finance':
		  window.location.href = '<?php echo base_url();?>student/card/'+id+'/finance';
		  break;
		}
	}
}

function resubscribe(){
	id=$('#selectbox').val();
	
	$.ajax({
		url: "<?php echo base_url()?>student/resubscribe",
		method: 'post',
        data: {regid: id},
     	dataType: 'json',
     	success: function(response){
			console.log(response);
			if (response.success=='true'){
				// $('#selectbox').select2("val", "");
				$('.alert > p').append('<span>Η επανεγγραφή ήταν επιτυχής! Μπορείτε να μεταβείτε στην καρτέλα του μαθητή πατώντας:</span>');
				$('.alert > p').append('<a href=<?php echo base_url();?>student/card/'+response.regid+' class="alert-link"> εδώ</a>');
				$('#resultmsg').removeClass();
				$('#resultmsg').addClass('alert');
				$('#resultmsg').addClass('alert-success');
				$(".alert").fadeIn();
			}
			else {
				if (response.reason=='sameSchYear'){
					$('.alert > p').append('<span>ΣΦΑΛΜΑ: Προσπαθείτε να επαννεγράψετε μαθητή στην ίδια διαχειριστική περίοδο! <a href=<?php echo base_url();?>term class="alert-link">Δημιουργήστε μια νέα</a>, επιλέξτε τη και κάντε από εκεί την επαννεγραφή. </span>');	
				}
				else {
					$('.alert > p').append('<span>ΣΦΑΛΜΑ: Η επανεγγραφή ΔΕΝ ήταν επιτυχής. </span>');
				}
				$('#resultmsg').removeClass();
				$('#resultmsg').addClass('alert');
				$('#resultmsg').addClass('alert-danger');
				$(".alert").fadeIn();
			}
		 }
	})
}

</script>


<!-- I removed tabindex="-1" from the modal to make the select2 work!!! but now pressing esc doesn't close the modal!-->
<div id="footerModal" class="modal" role="dialog" aria-labelledby="footerModalLabel" aria-hidden="true">
	<div class="modal-dialog">
      <div class="modal-content">	
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="footerModalLabel">Αναζήτηση μαθητή</h3><b>(Ctrl+rightarrow)</b>
		</div>
		<div class="modal-body">
			 <div class="form-group">
			 	<label for="single" class="control-label">Αναζήτηση:</label>
 			 	<input class="form-control" id="selectbox" type="hidden" name="optionvalue" style="width:100%"/>
			 </div>
			<div class="btn-group">
			    <a class="btn btn-sm btn-modal" href="#" onclick="fastgo('card');">Στοιχεία</a>
			    <a class="btn btn-sm btn-modal" href="#" onclick="fastgo('contact');">Επικοινωνία</a>
			    <a class="btn btn-sm btn-modal" href="#" onclick="fastgo('attendance');">Φοίτηση</a>
			    <a class="btn btn-sm btn-modal" href="#" onclick="fastgo('finance');">Οικονομικά</a>
			</div>
		</div>
		<div class="modal-footer">
		<button id="resubscribebtn" class="btn btn-danger" onclick="resubscribe();">Επανεγγραφή για το <?php echo $this->session->userdata('startsch');?></button>			
			<div class="alert alert-success" role="alert" id="resultmsg" style="display:none; margin-top:10px;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      			<p style="text-align:left;"> </p> 
			</div>

		</div>
		</div>
	</div>
</div>

<?php endif;?>

</body>

</html>
