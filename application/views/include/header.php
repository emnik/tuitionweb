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
   <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

   <link href="<?php echo base_url('assets/css/footable/footable-0.1.css') ?>" rel="stylesheet">
   
   <link href="<?php echo base_url('assets/css/custom.css') ?>" rel="stylesheet">

   <!-- Fonts -->
   <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

   <!-- visit http://www.google.com/fonts#UsePlace:use/Collection:Ubuntu to include more styles-->
   <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic&subset=latin,greek' rel='stylesheet' type='text/css'>
   

   <!-- Javascript & JQuery libs -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>   

   <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
   
   <script src="<?php echo base_url('assets/js/footable/footable.js') ?>"></script>

	



<!-- http://antesarkkinen.com/blog/easy-jquery-scroll-to-top-of-the-page-code-snippet/ -->
<script type="text/javascript">
   // scroll-to-top button show and hide
   jQuery(document).ready(function(){
       jQuery(window).scroll(function(){
           if (jQuery(this).scrollTop() > 100) {
               jQuery('.scrollup').fadeIn();
           } else {
               jQuery('.scrollup').fadeOut();
       }
   });
   // scroll-to-top animate
   jQuery('.scrollup').click(function(){
       jQuery("html, body").animate({ scrollTop: 0 }, 600);
           return false;
       });
   });
</script>

