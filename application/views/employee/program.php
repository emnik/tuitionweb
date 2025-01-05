<style type="text/css">
  .buttons a {
    margin-right: 14px;
  }
</style>


<script type="text/javascript">
var nodays = new Array(7);

    function togglebuttons(id,which){
         $('.toggle'+id).toggle();
         $('#table'+id).trigger($('.toggle'+id+':'+which).data('trigger')).trigger('footable_redraw');
    }
   

    function hideFootableExpandButtons(){
        if($('.footable').hasClass('default'))
        {
          $('.buttons').hide();
        }
        else
        {
          $('.buttons').show();
        }
  }


   $(document).ready(function() {

    //Menu current active links and Title
    // $('#menu-management').addClass('active');
    $('#menu-staff').addClass('active');
    $('#menu-header-title').text('Καρτέλα Εργαζομένου'); 

     $('#help').popover({
        placement:'bottom',
        container:'body',
        html:'true',       
        title:'<h4>Συνήθεις επεξεργασίες</h4>',
        content:"<ul><li>Για επεξεργασία προγράμματος ή/και αίθουσας ενός μαθήματος ή για να δείτε/επεξεργαστείτε τους μαθητές του τμήματος, πατήστε το κουμπί με το όνομα του τμήματος.</li></ul>"
     });

     $('#toggledays').tooltip({
        title:'Εμφάνιση όλων των ημερών',
        trigger:'hover',
        placement: 'right',
        container:'body'
     });

     $('.nolesson').parent().hide();
     
     for (var i = 0; i < nodays.length; i++) {
        if (typeof nodays[i] !== 'undefined') {
          $('#dayli'+nodays[i]).addClass('disabled');
        };
      };

      <?php for ($i=1; $i <= 7 ; $i++):?>
        $("#dayli<?php echo $i?>").on('click', function(e){
          e.preventDefault();
          $(".nav.nav-pills li a").each(function(){
            $(this).removeClass('active');
          });
          $('#toggledays').removeClass('active');
          $(this).addClass('active');
          $(".panel").parent().hide();
          $("#day<?php echo $i?>").show();
        })
       <?php endfor;?>

       $('#toggledays').attr('data-original-title', 'Εμφάνιση προγράμματος όλων των ημερών')
          .tooltip('fixTitle')
          // .tooltip('show')
          .on('click', function(e){
            e.preventDefault();
            $(this).addClass('active');
            $(".nav.nav-pills li a").each(function(){
              $(this).removeClass('active');
            });
            $('.panel:not(.nolesson)').parent().each(function(){
              $(this).show();
            })
          })
          
            $('.footable').footable();

     $(window).resize(function() {
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');
            }, 100);
        });


      $(window).on('resizeEnd', function() {
          //do something, window hasn't changed size in 100ms
          hideFootableExpandButtons();
      });

      $(window).on('load', function() {
           hideFootableExpandButtons();
      });

   });


</script>

</head>
<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->
    <!-- Menu start -->
    <!-- dirname(__DIR__) gives the path one level up by default -->
    <?php include(dirname(__DIR__).'/include/menu.php');?> 
    <!-- Menu end -->


<!-- main container
================================================== -->

  <div class="container" style="margin-bottom:60px;">
  
     
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Πρόγραμμα εργαζομένου</li>
        </ul>
      </div>
      
      
      <p>
        <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      </p>

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Προφίλ</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/gradebook" >Βαθμολόγιο</a></li>
      </ul>

      <?php $day=array(1 => 'Δευτέρα', 2 => 'Τρίτη', 3 => 'Τετάρτη', 4 => 'Πέμπτη',
                       5 => 'Παρασκευή', 6 => 'Σάββατο', 7 => 'Κυριακή');?>

      <div class="row">
        <div class="col-md-12">
          <div class="btn-toolbar" style="margin:15px 0px;">
            <a class="btn btn-default" href="<?php echo base_url();?>staff/card/<?php echo $employee['id']?>/teachingplan"><i class="icon-chevron-left"></i> πίσω</a>
            <a id="toggledays" class="btn btn-default active" href="#"><i class="icon-calendar"></i></a>
            <button type="button" class="btn btn-default pull-right" id="help">Βοήθεια</button>            
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
      		<h4>Εβδομαδιαίο πρόγραμμα :</h4>
          <ul class="nav nav-pills">
            <?php for ($i=1; $i <= 7 ; $i++):?>
            <li >
                <a class="btn btn-default" id="dayli<?php echo $i?>" href="#"><?php echo $day[$i];?></a>
            </li>
            <?php endfor;?>
          </ul>
        </div>
      </div>

      <p></p>

    	<div class="row">
      	<div class="col-md-12"> 
	      		<?php if(empty($program)):?>
	      			<p class="text-info">
	      				Δεν έχει εισαχθεί το πρόγραμμα για το συγκεκριμένο εργαζόμενο!
	      			</p>
	      		<?php else:?>
            <?php $l=0;
            while ($program[$l]['priority']==null){$l++;} //sections without program have priority=null!
            $i = $program[$l]['priority'];?>
            <?php $k=$l;?>
            <?php for ($j=1; $j<=7 ; $j++):?>
					    <div id="day<?php echo $j;?>" >
              <?php if($j<$i || $k==count($program)):?>
                <div class="panel panel-default nolesson">
                  <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-calendar"></i>
                    </span>
                    <h3 class="panel-title"><?php echo $day[$j];?></h3>
                  </div>
                <div class="panel-body">
                <p class="nolesson">Κανένα μάθημα</p>
                <script type="text/javascript">
                  nodays.push(<?php echo $j;?>);
                </script>
              <?php else:?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-calendar"></i>
                    </span>
                    <h3 class="panel-title"><?php echo $day[$j];?></h3>
                    <div class="buttons">
                        <a enabled data-trigger="footable_expand_all" onclick="togglebuttons(<?php echo $j;?>,'first');" class="toggle<?php echo $j;?> btn btn-default btn-sm" href="#expandall"><i class="icon-angle-down"></i></a>
                        <a enabled data-trigger="footable_collapse_all" onclick="togglebuttons(<?php echo $j;?>,'last');" style="display: none" class="toggle<?php echo $j;?> btn btn-default btn-sm" href="#collapseall"><i class="icon-angle-up"></i></a>
                    </div>                        
                  </div>
                <div class="panel-body">
                <table id="table<?php echo $j;?>" class="footable table table-striped table-condensed " style="text-align:center;">
			      			<thead>
                    <tr>
				      				<th  style="text-align:center;" data-class="expand">Ώρα</th>
				      				<th style="text-align:center;">Μάθημα</th>
				      				<th style="text-align:center;" data-hide="phone">Αίθουσα</th>
                      <th style="text-align:center;">Τμήμα</th>
                    </tr>
			      			</thead>
			      			<tbody>
                    <?php $stop=false;?>
                		<?php while($k<count($program) && $stop==false):?>
                      <?php if($program[$k]['priority']==$i):?>
				      					<tr>
				      						<td><?php echo date('H:i',strtotime($program[$k]['start_tm'])).'-'.date('H:i',strtotime($program[$k]['end_tm']));?></td>
				      						<td><?php echo $program[$k]['title'];?></td>
				      						<td><?php echo $program[$k]['classroom'];?></td>
                          <td><a style="font-weight:600;" class="label label-section" href="<?php echo base_url()?>section/card/<?php echo $program[$k]['section_id'];?>"><?php echo $program[$k]['section'];?></a></td>
				      					</tr>
			      					  <?php $k++;?>
                      <?php else:?>
                        <?php $stop=true;?>
                      <?php endif;?>
			      				<?php endwhile;?>
			      			</tbody>
			      		</table>

                <?php if($k<count($program)):?>
                  <?php $i=$program[$k]['priority'];?>
                <?php endif;?>
			      	<?php endif;?>
              </div> <!--Close contentbox class-->     
            </div>
          </div>           
        <?php endfor;?>
      <?php endif;?>
    	</div>
    </div>

  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->