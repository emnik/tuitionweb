<script type="text/javascript">


   $(document).ready(function() {
     $('.footable').footable();
   });


</script>

</head>
<body>
 <div class="wrapper"> <!--body wrapper for css sticky footer-->

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url()?>">TuitionWeb</a>
     </div>

      <div class="navbar-collapse collapse" role="navigation">
        <ul class="nav navbar-nav">
            <!-- <li><a href="<?php echo base_url()?>">Αρχική</a></li>  -->
            <li><a href="<?php echo base_url()?>student">Μαθητολόγιο</a></li>
            <li class="active"><a href="<?php echo base_url()?>staff">Προσωπικό</a></li>
            <li><a href="<?php echo base_url()?>section">Τμήματα</a></li>
            <li><a href="#finance">Οικονομικά</a></li>
            <li><a href="#reports">Αναφορές</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Χρήστης<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Νικηφορακης Μανος</li>
                <li><a href="#">Αλλαγή κωδικού</a></li>
                <li><a href="#admin">Διαχείριση</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo base_url()?>staff/logout">Αποσύνδεση</a></li>
              </ul>
            </li>
        </ul>
      </div><!--/.navbar-collapse -->
    </div>
  </div>


<!-- Subhead
================================================== -->
<div class="jumbotron subhead">
  <div class="container">
    <h1>Καρτέλα Εργαζομένου</h1>
    <p class="leap">Πρόγραμμα διαχείρισης φροντιστηρίου.</p>
  </div>
</div>


<!-- main container
================================================== -->

  <div class="container" style="margin-bottom:60px;">
        
      <div>
        <ul class="breadcrumb">
          <li><a href="<?php echo base_url()?>"><i class="icon-home"> </i> Αρχική </a></li>
          <li><a href="<?php echo base_url()?>staff">Προσωπικό</a> </li>
          <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Καρτέλα εργαζομένου</a> </li>
          <li class="active">Τμήματα</li>
        </ul>
      </div>
      
      <p>
        <h3><?php echo $employee['surname'].' '.$employee['name']?></h3>
      </p>

      <ul class="nav nav-tabs">
        <li><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>">Στοιχεία</a></li>
        <li class="active"><a href="<?php echo base_url()?>staff/card/<?php echo $employee['id']?>/teachingplan">Πλάνο Διδασκαλίας</a></li>
      </ul>

      <div class="row">
        <div class="col-md-12">
          <div class="btn-toolbar" style="margin:15px 0px;">
            <a class="btn btn-default" href="<?php echo base_url();?>staff/card/<?php echo $employee['id']?>/teachingplan"><i class="icon-chevron-left"></i> πίσω</a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
      		<h4>Τμήματα:</h4>
        </div>
        <div class="col-md-10">
          <ul class="nav nav-pills">
            <?php foreach ($section as $data):?>
            <li>
                <a href="#<?php echo $data['id']?>"><?php echo $data['section'].'/'.mb_substr($data['title'], 0, 3, 'UTF-8' /* (the correct encoding) */);?></a>
            </li>
            <?php endforeach;?>
          </ul>
        </div>
      </div>

      <p></p>

    	<div class="row">
      	<div class="col-md-12"> 
	      		<?php if(empty($section)):?>
	      			<p class="text-info">
	      				Δεν έχουν αντιστοιχιστεί τμήματα για το συγκεκριμένο εργαζόμενο!
	      			</p>
	      		<?php else:?>
              <?php foreach ($section_data as $key=>$value):?>
                  <div class="panel panel-default" id="<?php echo $value[0]['id'];?>">
                   <div class="panel-heading">
                    <span class="icon">
                      <i class="icon-calendar"></i>
                    </span>
                    <h3 class="panel-title"><?php echo $value[0]['section'].' / '.$value[0]['title'];?></h3>
                  </div>
                  <div class="panel-body">
                    <table class="footable table table-striped table-condensed " >
                      <thead>
                        <tr>
                          <th data-class="expand">Ονοματεπώνυμο</th>
                          <th data-hide="phone">Σταθερό</th>
                          <th>Κινητό</th>
                          <th data-hide="phone,tablet">Πατρώνυμο</th>
                          <th data-hide="phone,tablet">Κινητό Πατέρα</th>
                          <th data-hide="phone">Μητρώνυμο</th>
                          <th data-hide="phone">Κινητό Μητέρας</th>
                          <th data-hide="phone,tablet">Δουλειάς</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($value as $data):;?>
                        <tr>
                          <td><?php echo $data['stdname'];?></td>
                          <td><?php echo $data['home_tel'];?></td>
                          <td><?php echo $data['std_mobile'];?></td>
                          <td><?php echo $data['fathers_name'];?></td>
                          <td><?php echo $data['fathers_mobile'];?></td>
                          <td><?php echo $data['mothers_name'];?></td>
                          <td><?php echo $data['mothers_mobile'];?></td>
                          <td><?php echo $data['work_tel'];?></td>
                        </tr>
                      <?php endforeach;?>
                      </tbody>
                    </table>
                  </div>
                </div>
            <?php endforeach;?>
          <?php endif;?>

      </div>


  </div> <!--end of main container-->

<div class="push"></div>
</div> <!-- end of body wrapper-->