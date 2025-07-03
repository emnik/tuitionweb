<!DOCTYPE html>
<html lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="">
   <meta name="keywords" content="">
   <meta name="author" content="Nikiforakis Manos">

   <title>Πρόγραμμα Διαχείρισης Φροντιστηρίου</title>

   <!--the css file ordering is important to work everything as intented. Do not change! -->
  
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

   <link rel="icon" href="<?=base_url()?>/favicon-96x96.png" type="image/png">

   <link href="<?php echo base_url('assets/FooTable-2/css/footable.core.css') ?>" rel="stylesheet">



   <!-- CKeditor -->
   <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.css">
   <link href="<?php echo base_url('assets/css/ckeditor.css') ?>" rel="stylesheet">

   <!-- Fonts -->
   <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

   <!-- visit http://www.google.com/fonts#UsePlace:use/Collection:Ubuntu to include more styles-->
   <!-- <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic&subset=latin,greek' rel='stylesheet' type='text/css'> -->
   

   <!-- Javascript & JQuery libs -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>   
  

   <!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script> -->
   <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
   
   <script src="<?php echo base_url('assets/FooTable-2/js/footable.js') ?>"></script>
 
   <!-- CDN for select2 Newer Version -->
   <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 
   <!-- Themming -->
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('theme-styles'); ?>"> <!-- Set the :root CSS variables! -->
   <!--base_url('theme-styles') is defined in application > config > routes.php -->
   <link href="<?php echo base_url('assets/css/custom.css') ?>" rel="stylesheet">    


<!-- http://antesarkkinen.com/blog/easy-jquery-scroll-to-top-of-the-page-code-snippet/ -->
<script type="text/javascript">
//    // scroll-to-top button show and hide
//    jQuery(document).ready(function(){
//        jQuery(window).scroll(function(){
//            if (jQuery(this).scrollTop() > 100) {
//                jQuery('.scrollup').fadeIn();
//            } else {
//                jQuery('.scrollup').fadeOut();
//        }
//    });
//    // scroll-to-top animate
//    jQuery('.scrollup').click(function(){
//        jQuery("html, body").animate({ scrollTop: 0 }, 600);
//            return false;
//        });
//    });

   jQuery(document).ready(function(){
        jQuery('.br-icon').click(function(){
            jQuery('#footerModal').modal();
        });
   });


</script>
<script src="<?php echo base_url('assets/js/custom.js') ?>"></script>
