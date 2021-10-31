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

<!-- Using https://github.com/ivaynberg/select2 -->
<!-- with https://github.com/t0m/select2-bootstrap-css -->
<link href="<?php echo base_url('assets/select2/select2.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/select2/select2-bootstrap.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/select2/select2.js')?>"></script>
<script src="<?php echo base_url('assets/select2/select2_locale_el.js')?>"></script>

<script type="text/javascript">

$(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '39')) { //ctrl + right arrow
            event.preventDefault();
            $('#footerModal').modal();
        }
    });


$(document).ready(function(){
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
	    minimumInputLength: 2,
	    ajax: {
	      url: "<?php echo base_url()?>welcome/user_list",
	      dataType: 'json',
	      data: function (term, page) {
	        return {
	          q: term //sends the typed letters to the controller
	        };
	      },
	      results: function (data, page) {
	        return { results: data }; //data needs to be {{id:"",text:""},{id:"",text:""}}...
	      }
	    }
	  });

	   $('#footerModal').on('shown.bs.modal', function(){
			$('#selectbox').select2("open");
	   });

	   $('#selectbox').on('change', function(){
			$(".alert").hide();
			$('.alert > p > span').remove();
			$('.alert > p > a').remove();
			id=$(this).val();
	   })
});


function fastgo(section){
	id=$('#selectbox').val();
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
				$('.alert > p').append('<span>ΣΦΑΛΜΑ: Η επανεγγραφή ΔΕΝ ήταν επιτυχής. </span>');
				$('#resultmsg').removeClass();
				$('#resultmsg').addClass('alert');
				$('#resultmsg').addClass('alert-danger');
				$(".alert").fadeIn();
			}
		 }
	})
}

</script>

<style type="text/css">
	#footerModal .modal-body {
		/*max-height: 700px;*/
    	overflow: visible;
	}
</style>

<!-- I removed tabindex="-1" from the modal to make the select2 work!!! but now pressing esc doesn't close the modal!-->
<div id="footerModal" class="modal" role="dialog" aria-labelledby="footerModalLabel" aria-hidden="true">
	<div class="modal-dialog">
      <div class="modal-content">	
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="footerModalLabel">Γρήγορη εναλλαγή μαθητή</h3>
		</div>
		<div class="modal-body">
			 <div class="form-group">
			 	<label for="single" class="control-label">Επιλέξτε μαθητή/μαθήτρια:</label>
 			 	<input class="form-control" id="selectbox" type="hidden" name="optionvalue" />
			 </div>
			<div class="btn-group">
			    <a class="btn btn-sm btn-default" href="#" onclick="fastgo('card');">Στοιχεία</a>
			    <a class="btn btn-sm btn-default" href="#" onclick="fastgo('contact');">Επικοινωνία</a>
			    <a class="btn btn-sm btn-default" href="#" onclick="fastgo('attendance');">Φοίτηση</a>
			    <a class="btn btn-sm btn-default" href="#" onclick="fastgo('finance');">Οικονομικά</a>
			</div>
		</div>
		<div class="modal-footer">
			<!-- <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button> -->
			<button class="btn btn-primary" onclick="resubscribe();">Επανεγγραφή</button>
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
