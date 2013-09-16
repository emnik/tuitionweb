    <footer class="footer">
        <div class="container">
			<div class="row">
				<div class="col-md-12"> 
					<small><i class="icon-code"></i> Designed and build by <a href="mailto:nikiforakis.m@gmail.com">Nikiforakis Manos</a></small>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12"> 
					<small>Build with <a href="http://ellislab.com/codeigniter">CodeIgniter</a> and <a href="http://getbootstrap.com/">Bootstrap</a>. Icons by <a href="http://fortawesome.github.io/Font-Awesome/"><i class="icon-flag"> </i>Font Awesome Icons</a></small>
				</div>
			</div>
		</div>
	</footer>
	
	<a class="scrollup" href="#">Scroll</a>



<?php if(!empty($regs)):?>

<!-- I will use select2 from http://fk.github.io/select2-bootstrap-css/ -->
<!-- <link href="<?php echo base_url('assets/css/bootstrap-combobox.css')?>" rel="stylesheet">
<script src="<?php echo base_url('assets/js/bootstrap-combobox.js')?>"></script>
 -->
<script type="text/javascript">
$(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '39')) { //ctrl + right arrow
            event.preventDefault();
            $('#footerModal').modal();
        }
    });

$(document).ready(function(){
	//$('.combobox').combobox();
	// $('#footerModal').on('shown', function(){
	// 	$(this).find(".modal-body > #combo").focus()
	// });
});

function fastgo(section){
	var combobox = document.getElementById('combo');
	id = combobox.value;
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
</script>

<style type="text/css">
	#footerModal .modal-body {
		/*max-height: 700px;*/
    	overflow: visible;
	}
</style>


<div id="footerModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="footerModalLabel" aria-hidden="true">
	<div class="modal-dialog">
      <div class="modal-content">	
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="footerModalLabel">Γρήγορη εναλλαγή μαθητή</h3>
		</div>
		<div class="modal-body">
			<p>Επιλέξτε μαθητή:</p>
			<p>
				<select class="combobox form-control" id="combo">
					<?php foreach ($regs as $key => $value):?>
						<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php endforeach;?>
				</select>
			</p>
			<div class="btn-toolbar">
			    <a class="btn btn-default" href="#" onclick="fastgo('card');">Στοιχεία</a>
			    <a class="btn btn-default" href="#" onclick="fastgo('contact');">Επικοινωνία</a>
			    <a class="btn btn-default" href="#" onclick="fastgo('attendance');">Φοίτηση</a>
			    <a class="btn btn-default" href="#" onclick="fastgo('finance');">Οικονομικά</a>
			</div>
		</div>
		<div class="modal-footer">
			<!-- <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button> -->
			<button class="btn btn-primary">Επανεγγραφή</button>
		</div>
		</div>
	</div>
</div>

<?php endif;?>







</body>

</html>
