<style type="text/css">
</style>

<script type="text/javascript">
var undoarr=[]; //undo array
var editClassOn = false;

function toggleedit(togglecontrol, id) {
  if ($(togglecontrol).hasClass('active')){
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).attr('disabled', 'disabled');
      $(this).find('btn').attr('disabled','disabled');
      });
      $('#submitbtn').attr('disabled', 'disabled');
      $('#cancelbtn').attr('disabled', 'disabled');
      $('#undobtn').attr('disabled', 'disabled');
    }
    else {
    $('#' + id).closest('.mainform').find(':input').each(function(){
      $(this).removeAttr('disabled');
      });
      $(this).find('btn').removeAttr('disabled');
      $('#submitbtn').removeAttr('disabled');
      $('#cancelbtn').removeAttr('disabled');
      $(".alert").fadeIn();
      if(undoarr.length>0){
        $('#undobtn').removeAttr('disabled');
      }
    }
    $('#classes').removeAttr('disabled');
}


    var courserowtemplate, lessonrowtemplate; //globals
    var newcoursec=0, newlessonc=0; //globals



    $(document).on('click','.addcoursebtn', function(){
        newcoursec++;
        newlessonc++; //every time we add a course it is also added one new lesson field!
        var newcourserow = courserowtemplate.clone();
        newcourserow.removeClass('hidden');
        newcourserow.attr('id',-newcoursec);
        var fields=newcourserow.find('input');
        var selectfields = newcourserow.find('select');
        fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
        undoarr.push('courseid['+(-newcoursec)+']');
        fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
        selectfields.eq(0).attr('id', 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']');
        selectfields.eq(0).attr('name', 'title['+(-newcoursec)+"]["+(-newlessonc)+']');
        var whereaddcourserow = $(this).parents('.courserow');
        newcourserow.insertAfter(whereaddcourserow);      
        var newlessonid = 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']'
        $(jq(newlessonid)).prop('disabled',false);
        $('#'+(-newcoursec)).find('input:first').focus();
        $('#undobtn').removeAttr('disabled');
    });
 
    $(document).on('click', '.addlessonbtn', function(){
          newlessonc++;
          var courseid = $(this).parents('.courserow').attr('id');
          var newlessonrow = lessonrowtemplate.clone();
          newlessonrow.removeClass('hidden');
          var fields=newlessonrow.find('input');
          var selectfields = newlessonrow.find('select');
          selectfields.eq(0).attr('id', 'lessonid['+courseid+']['+(-newlessonc)+']');
          undoarr.push('lessonid['+courseid+']['+(-newlessonc)+']');
          selectfields.eq(0).attr('name', 'title['+courseid+']['+(-newlessonc)+']');
          var whereaddlessonrow = $(this).parents('.lessonrow');
          newlessonrow.insertAfter(whereaddlessonrow);
          var newlessonid = 'lessonid['+courseid+']['+(-newlessonc)+']';
          $(jq(newlessonid)).prop('disabled',false);
          $('#undobtn').removeAttr('disabled');
    })
    
    
    //some characters needs to be escaped in order to recognize the id
    //http://learn.jquery.com/using-jquery-core/faq/how-do-i-select-an-element-by-an-id-that-has-characters-used-in-css-notation/
    function jq( myid ) {
    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
    }
    

    $(document).on('click', '#undobtn', function(){
      var id = undoarr.pop();
      if(id.substring(0,8)=='lessonid'){
        $(jq(id)).parents('.lessonrow').remove();
      }
      else if (id.substring(0,8)=='courseid'){
        $(jq(id)).parents('.courserow').remove(); 
      }
      if(undoarr.length==0)
        {
          $('#undobtn').attr('disabled','disabled');
          var visiblecourses = $('.courserow:visible');
          if(visiblecourses.length==0) {
            $("#classes option").removeAttr('selected');
            $("#classes option:first").attr("selected", "selected");
            $('#editform1').attr('disabled', 'disabled');
            $('#submitbtn').attr('disabled', 'disabled');
            $('#cancelbtn').attr('disabled', 'disabled');
          }
        }
    });

    $(document).on('click', '.delcoursebtn', function(){

        var rowcourseid = $(this).parents('.courserow').attr('id');
        var courseid = $(this).parent().parent().parent().next('input').attr('id');
        var courses = $('.courserow:visible');        
        var whereaddcourserow = $(this).parents('.panel-body');

        //remove course id from undoarr
        var a = $.inArray(courseid,undoarr);
        if(a!==-1){
          undoarr.splice(a,1);
        }
        
        //find the lessonids - if any - corresponding in this course and store their positions in undoarr in another array named removeidspos
        if (Math.abs(rowcourseid)<10 ){
          var slength = 12; //I need slength to know how many characters to remove with substring
        }
        else
        {
          var slength=13;
        }
        removeidspos=[];
        for (var i = 0; i < undoarr.length; i++) {
          var v = undoarr[i].substring(0,slength);
          if (v=='lessonid['+rowcourseid+']'){
            removeidspos.push(i);
          }
        };
        //remove the values from undoarr using reverse iteration as not to change the positions when removing each item !!!
        for (var i = removeidspos.length-1; i >= 0; i--)
        {
          undoarr.splice(removeidspos[i],1);
        }
        if(undoarr.length==0)
        {
          $('#undobtn').attr('disabled','disabled');
        }


        //if it is a new course..
        if (rowcourseid<0){
          $(this).parents('.courserow').remove();  
        }
        else
        {
          var r=confirm('Πρόκειται να διαγράψετε μία κατεύθυνση. Συνίσταται να μην το κάνετε αν έχετε αντιστοιχίσει έστω και 1 μαθητή σε αυτήν, ακόμα και σε παλαιότερη σχολική χρονιά. Μαζί με την κατεύθυνση θα διαγραφούν και όλα τα μαθήματα που τυχών έχετε αντιστοιχίσει σε αυτήν. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.')
          if (r==true)
          {
            post_url = '<?php echo base_url("curriculum/delcourse");?>'; 
            $.ajax({
              global: false,
              type: "post",
              url: post_url,
              data : {'jscourseid':rowcourseid},
              dataType:'json', 
              success: function(){
                $('#'+rowcourseid).remove();
              }
            }); //end of ajax
          }
        }

        //if no courses remain in a class we have to insert a new course/lesson field ready to be populated!
        if (courses.length==1)
        {
          newcoursec++;
          newlessonc++; //every time we add a course it is also added one new lesson field!
          var newcourserow = courserowtemplate.clone();
          newcourserow.removeClass('hidden');
          newcourserow.attr('id',-newcoursec);
          var fields=newcourserow.find('input');
          var selectfields=newcourserow.find('select');
          fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
          undoarr.push('courseid['+(-newcoursec)+']');
          fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
          selectfields.eq(0).attr('id', 'lessonid['+(-newcoursec)+']['+(-newlessonc)+']');
          selectfields.eq(0).attr('name', 'title['+(-newcoursec)+']['+(-newlessonc)+']');
          newcourserow.appendTo(whereaddcourserow);
          var newlessonid = 'lessonid['+(-newcoursec)+']['+(-newlessonc)+']';
          $(jq(newlessonid)).prop('disabled',false);
          $('#'+(-newcoursec)).find('input:first').focus();
          $('#undobtn').removeAttr('disabled');
        }
    });

    $(document).on('click', '.dellessonbtn', function(){
        var courselessons = $(this).parents('.courserow').find('.lessonrow'); //how many lessons exist in current course
        var whereaddlessonrow = $(this).parents('.col-sm-6'); //the div that contains the lessons for the current course 
        var courseid = $(this).parents('.courserow').attr('id');//the courseid
        var lessonid = $(this).parent().parent().prev().find('select').attr('id');//the lessonid from the select input
        if ($.inArray(lessonid, undoarr)!==-1)
        {
          undoarr.splice($.inArray(lessonid, undoarr),1);
          $(this).parents('.lessonrow').remove();
          if(undoarr.length==0)
          {
            $('#undobtn').attr('disabled','disabled');
          }
        }
        else
        //it is an old record
        {
          var a=[];
          var jslessonidtmp = lessonid;
          jslessonidtmp.replace(/\[(.+?)\]/g, function($0, $1) { a.push($1) });
          var jslessonid = a[1];
          var r=confirm('Πρόκειται να διαγράψετε ένα μάθημα. Συνίσταται να μην το κάνετε αν έχετε αντιστοιχίσει έστω και 1 μαθητή σε αυτό, ακόμα και σε παλαιότερη σχολική χρονιά. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.')
          if (r==true)
          {
            post_url = '<?php echo base_url("curriculum/dellesson");?>'; 
            $.ajax({
              global: false,
              type: "post",
              url: post_url,
              data : {'jslessonid':jslessonid},
              dataType:'json', 
              success: function(){
                $(jq(lessonid)).parents('.lessonrow').remove();
              }
            }); //end of ajax
          }
        }

        //if no lessons remain in a course we have to insert a new lesson field ready to be populated with a new lesson!
        if (courselessons.length==1)
        {
          newlessonc++;
          var newlessonrow = lessonrowtemplate.clone();
          newlessonrow.removeClass('hidden');
          var fields=newlessonrow.find('input');
          var selectfields=newlessonrow.find('select');
          selectfields.eq(0).attr('id', 'lessonid['+courseid+']['+(-newlessonc)+']');
          selectfields.eq(0).attr('name', 'title['+courseid+']['+(-newlessonc)+']');
          newlessonrow.appendTo(whereaddlessonrow);
          var newlessonid = 'lessonid['+courseid+']['+(-newlessonc)+']';
          $(jq(newlessonid)).prop('disabled',false);
        }
      // }
    });

    $(document).ready(function(){

        //Menu current active links and Title
        $('#menu-management').addClass('active');
        $('#menu-curriculum').addClass('active');
        $('#menu-header-title').text('Πρόγραμμα Σπουδών');

        courserowtemplate = $('.courserow:hidden'); //store the template to be cloned
        lessonrowtemplate = $('.lessonrow:hidden'); //store the template to be cloned
        $('#editform1').attr('disabled', 'disabled');

       
        $.ajax({
          type: "POST",
          url: "<?php echo base_url()?>curriculum/lessontitles/",
          dataType:'json',
          success: function(lessontitles){
            // titles = lessontitles;
            $.each(lessontitles,function(id,text) 
            {
              // here we're creating a new select option for each group
              var opt = $('<option />');
              opt.val(id);
              opt.text(text);
              $('select:hidden').append(opt); 
            });
            $('select:hidden').val("");
            $('select:hidden').prop('value', "");
            $('select:hidden').attr('value', "");
            } 
        });

        $('#cancelbtn').click(function(){
          window.open("<?php echo base_url('curriculum/cancel');?>", '_self', false);
        });

        $("body").on('click', '#editform1, #editform2', function(){
          toggleedit(this, this.id);
          $(this).removeAttr('disabled');

        });


        $('#classes').on('change', function(){
          if(editClassOn) return;
          $('#editclassrow').hide();
          $('.progress').show();
            getcourses();
            $( document ).ajaxComplete(function() {
                $('.mainform').find(':input:visible').attr('disabled','disabled');
                $('#submitbtn').attr('disabled','disabled');
                $('#cancelbtn').attr('disabled','disabled');
                $('#undobtn').attr('disabled','disabled');
                $('#classes').removeAttr('disabled');
                $('#editform1').removeClass('active');
                $('#editform1').removeAttr('disabled');
                if($('#classes').val()==0)
                {
                  $('#editform1').attr('disabled', 'disabled');
                  $('#submitbtn').attr('disabled', 'disabled');
                  $('#cancelbtn').attr('disabled', 'disabled');
                  $('#addclassbtn').removeAttr('disabled');      
                }
            }); 
        });


        $("body").on('click','#addclassbtn', function(){
            editClassOn = true;
            $('#classes').val(0);
            editClassOn = false;
            $('#editclassrow').find('label').text('Όνομα τάξης:');
            $('#editclassrow').show();
            $('#editform1').removeClass('active');
            $('#editform1').removeAttr('disabled');
            $('#submitbtn').removeAttr('disabled');
            $('#cancelbtn').removeAttr('disabled');
            $('.courserow:visible').remove();          
        })

        $("body").on('click','#editclassbtn', function(){
            classid = $('#classes').val();
            if(classid!=0){
              console.log(classid);
              $('#editclassrow').find('label').text('Νέο Όνομα:');
              $('#editclassrow').show();

              $('#editform1').removeClass('active');
              $('#editform1').removeAttr('disabled');
              $('#submitbtn').removeAttr('disabled');
              $('#cancelbtn').removeAttr('disabled');
              $('.courserow:visible').remove();          
            }
            else {
              console.log('no class selected to edit!')
            }
        })

        $("body").on('click','#delclassbtn', function(){
          classid = $('#classes').val();
          console.log(classid);
          if(classid!=0){
          var r=confirm('Πρόκειται να διαγράψετε μία τάξη. Συνίσταται να μην το κάνετε αν έχετε αντιστοιχίσει έστω και 1 μαθητή σε αυτήν, ακόμα και σε παλαιότερη σχολική χρονιά. Μαζί με την τάξη θα διαγραφούν και όλές οι κατευθύνσεις και τα μαθήματα που τυχών έχετε αντιστοιχίσει σε αυτήν. Η ενέργεια αυτή δεν αναιρείται. Παρακαλώ επιβεβαιώστε.')
          if (r==true)
          {
            post_url = '<?php echo base_url("curriculum/delclass");?>'; 
            $.ajax({
              global: false,
              type: "post",
              url: post_url,
              data : {'jsclassid':classid},
              dataType:'json', 
              success: function(){
                $('#classes option[value="'+classid+'"').remove();
                $('.courserow:visible').remove();   
              }
            }); //end of ajax
          }
          }
        })
        


    }) //end of (document).ready(function())

function getcourses(){

        var bar = $('.bar');

        var classid = $('#classes option:selected').val();

        $('.courserow:visible').remove();

        var postdata = {'jsclassid': classid};
        var post_url = "<?php echo base_url()?>curriculum/courses";
        $.ajax({
          type: "POST",
          url: post_url,
          data : postdata,
          dataType:'json',
          // async: false,
          success: function(courses) 
            {
              if(courses){
                //get the courselegth for determining the progress bar length
                var courselength = 0;
                  for (var key in courses) {
                    if (courses.hasOwnProperty(key)) ++courselength;
                  }
                var percent=0; //counter

              $.each(courses,function(id,course) 
                {
                  percent++; //counter raise
                  
                  newcourserow = courserowtemplate.clone();
                  newcourserow.removeClass('hidden');
                  var whereaddcourserow = $('.courserow:last');
                  var fields = newcourserow.find('input');
                  var selectfields = newcourserow.find('select');
                  fields.eq(0).attr("name", "course[" + id +"]");        
                  fields.eq(0).attr('id', "courseid["+id+"]");
                  fields.eq(0).prop('value', course);
                  fields.eq(0).attr('value', course);
                  newcourserow.attr('id', id);
                  newcourserow.insertAfter(whereaddcourserow);
                  bar.width(100*percent/courselength + '%'); //progress bar update
                  var newpostdata = {'jsclassid': classid, 'jscourseid': id};
                  var post_url = "<?php echo base_url()?>curriculum/lessons";
                  $.ajax({
                    type: "POST",
                    url: post_url,
                    data : newpostdata,
                    dataType:'json',
                    async:false, //needed for progress bar
                    success: function(lessons) 
                      {
                        if(lessons){
                           var firstcataloglessonid = lessons[Object.keys(lessons)[0]]['cataloglesson_id'];
                           var firstlessontext = lessons[Object.keys(lessons)[0]]['title'];
                           var firstid = Object.keys(lessons)[0];
                           selectfields.eq(0).attr("name", "title[" + id +"][" + firstid +"]");
                           selectfields.eq(0).attr('id', 'lessonid['+id+']['+firstid+']');
                           for (var i = 1; i < Object.keys(lessons).length; i++) {
                              var lessonid = Object.keys(lessons)[i];
                              var cataloglessonid = lessons[Object.keys(lessons)[i]]['cataloglesson_id'];
                              var lessontext = lessons[Object.keys(lessons)[i]]['title'];
                              
                              var newlessonrow = lessonrowtemplate.clone();
                              newlessonrow.removeClass('hidden');
                              var subfields = newlessonrow.find('input');
                              var subselectfields = newlessonrow.find('select');

                              subselectfields.eq(0).attr("name", "title["+id+"]["+lessonid +"]");        
                              subselectfields.eq(0).attr('id', "lessonid["+id+"]["+lessonid+"]");
                              var whereaddlessonrow = $('#'+id+' .lessonrow:visible:last');
                              newlessonrow.insertAfter(whereaddlessonrow);

                              var nextlessonid = "lessonid["+id+"]["+lessonid+"]";
                              $(jq(nextlessonid)).val(cataloglessonid);
                              $(jq(nextlessonid)).prop('value', cataloglessonid);
                              $(jq(nextlessonid)).attr('value', cataloglessonid);
                              $(jq(nextlessonid)).prop('disabled',false);
                            }

                            var firstlessonid = 'lessonid['+id+']['+firstid+']';
                            $(jq(firstlessonid)).val(firstcataloglessonid);
                            $(jq(firstlessonid)).prop('value', firstcataloglessonid);
                            $(jq(firstlessonid)).attr('value', firstcataloglessonid);
                            $(jq(firstlessonid)).prop('disabled',false);
                          }
                          else
                          {
                            newlessonc++;
                            var newlessonid = 'lessonid['+id+']['+(-newlessonc)+']';
                            selectfields.eq(0).attr('id', newlessonid);
                            selectfields.eq(0).attr("name", "title[" + id +"][" + (-newlessonc) +"]");
                            undoarr.push(newlessonid);
                            $(jq(newlessonid)).prop('disabled',false);
                          }                                  
                        }
                  })
                })
             
              }
              else if(classid!=0)
              {
                bar.width('100%');
                newcoursec++;
                newlessonc++; //every time we add a course it is also added one new lesson field!
                var newcourserow = courserowtemplate.clone();
                newcourserow.removeClass('hidden');
                newcourserow.attr('id',-newcoursec);
                var fields=newcourserow.find('input[type=text]');
                var selectfields = newcourserow.find('select');
                fields.eq(0).attr('id', 'courseid['+(-newcoursec)+']');
                undoarr.push('courseid['+(-newcoursec)+']');
                fields.eq(0).attr('name', 'course['+(-newcoursec)+']');
                selectfields.eq(0).attr('id', 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']');
                selectfields.eq(0).attr('name', 'title['+(-newcoursec)+"]["+(-newlessonc)+']');
                var whereaddcourserow = $('.courserow:hidden');
                newcourserow.insertAfter(whereaddcourserow);
                var newlessonid = 'lessonid['+(-newcoursec)+"]["+(-newlessonc)+']';
                $(jq(newlessonid)).prop('disabled',false);
                $('#undobtn').removeAttr('disabled');
              }
            } //end success
          })
          .done(function(){
            window.setTimeout(function(){
              bar.width(0);
              $('.progress').hide();              
            },300);
          }) //end AJAX

}


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

  <div class="container"  style="margin-bottom:60px;">
           
      <div>
	      <ul class="breadcrumb">
	        <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
	        <li class="active">Πρόγραμμα Σπουδών</li>
	      </ul>
      </div>

     <!-- <p><h3>Επεξεργασία προγράμματος σπουδών</h3></p> -->
      
      <ul class="nav nav-tabs" style="margin-bottom:15px;">
        <li class="active"><a href="<?php echo base_url('/curriculum')?>">Πρόγραμμα Σπουδών</a></li>
        <li><a href="<?php echo base_url('/curriculum/edit/tutorsperlesson')?>">Μαθήματα & Διδάσκωντες</a></li>
      </ul>


	<div class="row">
   <div class="col-md-12">
    <div class="alert alert-danger" style="display:none;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span style="font-family:'Play';font-weight:700;">ΠΡΟΣΟΧΗ! </span> Αποφεύγετε τις διαγραφές κατευθύνσεων ή/και μαθημάτων μιας και θα επηρρεάσουν άμεσα τα ήδη καταχωρημένα δεδομένα μαθητών που έχουν αντιστοιχιστεί στις κατευθύνσεις ή/και τα μαθήματα αυτά.
    </div>
     <form action="<?php echo base_url('/curriculum/edit')?>" method="post" accept-charset="utf-8" role="form">
     	<div class="row"> 
        <div class="col-md-12" id="group1">
		    	 <div class="mainform">
              <div class="panel panel-default">
       	        <div class="panel-heading">
              		<span class="icon">
               			<i class="icon-sitemap"></i>
            			</span>
            			<h3 class="panel-title">Πρόγραμμα Σπουδών</h3>
              			<div class="buttons">
                			<button id="editform1" type="button" class="btn btn-default btn-sm" data-toggle="button"><i class="icon-edit"></i></button>
              			</div>
          		  </div>
	              <div class="panel-body">
        	        <div class="row">	
                  <div class="col-xs-7 col-sm-5 col-md-4">
                      <div class="form-group">
                        <label>Επιλέξτε Τάξη:</label>
                        <select id="classes" class="form-control" name="class_name">
                        <option value="0"></option>  
                          <?php if(!empty($class)):?>
                            <?php foreach ($class as $data):?>
                              <option value="<?php echo $data['id'];?>"><?php echo $data['class_name'];?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        </div>
                    </div>
                    <div id="classfunctions" class="col-xs-5 col-sm-5 col-md-4">
                      <div class="btn-group" style="margin-top:24px;">
                        <button type="button" disabled id="delclassbtn" class="btn btn-default"><i class="icon icon-trash"></i></button>
                        <button type="button" disabled id="editclassbtn"class="btn btn-default"><i class="icon icon-edit"></i></button>
                        <button type="button" id="addclassbtn" class="btn btn-default"><i class="icon icon-plus"></i></button>
                      </div>
                    </div>
                  </div>
                  <div id="editclassrow" class="row" style="display:none;">
                    <div class="col-xs-7 col-sm-5 col-md-4">
                      <div class="form-group">
                        <label for="inputClassName">Όνομα τάξης:</label>
                          <input type="text" class="form-control" id="inputClassName" placeholder="" name="class_name_text">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="progress" style="display:none;">
                        <div class="progress-bar progress-bar-info bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        </div>
                      </div>
                    </div>
                  </div>
<!-- template row start -->
                  <div class="row courserow hidden">
                    <div class="col-sm-6"> <!-- Κατεύθυνση -->
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label>Κατεύθυνση</label>
                          <div class="input-group">
                            <input type="text" class="form-control" value="" placeholder="Πληκτρ/στε ένα όνομα...">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-default delcoursebtn"><i class="icon-trash"> </i></a></button>
                              <button type="button" class="btn btn-default addcoursebtn"><i class="icon-plus"> </i></a></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="col-xs-12 lessonrow"> <!-- Μαθημα -->
                        <div class="col-xs-9">
                          <div class="form-group">
                            <label>Μάθημα</label>
                              <select class="form-control">
                                <option></option>
                              </select>
                          </div>
                        </div>
                      <div class="col-xs-3 pull-right" style="padding-left:0px;padding-right:0px;"> <!-- Ώρες -->
                        <div class="btn-group" role="group" style="margin-top:24px;">
                          <button type="button" class="btn btn-default dellessonbtn"><i class="icon-trash"> </i></a></button>
                          <button type="button" class="btn btn-default addlessonbtn"><i class="icon-plus"> </i></a></button>
                        </div>
                      </div>
                     </div>
                    </div>
                  </div> 
  <!-- template row end -->
         	    	</div>
              </div>
            </div>
   	      </div>
  	    </div>
      </div>
  	</div>

    <div class="row">
    	<div class="col-md-12">    
        <div class="btn-toolbar">
          <div class="btn-group">
          	<button disabled id="cancelbtn" type="button" class="btn btn-default">Ακύρωση</button>
          </div>
          <div class="btn-group pull-right">
            <button disabled id="submitbtn" type="submit" class="btn btn-primary">Αποθήκευση</button>
            <button disabled id="undobtn" type="button" class="btn btn-primary pull-right"><i class="icon-undo"></i></button>
          </div>
      	</div>
      </div>
    </div>

   </form>

  </div>

  <div class="push"></div>

</div> <!-- end of body wrapper-->

