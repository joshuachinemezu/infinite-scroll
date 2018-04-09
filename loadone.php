<?php
	require_once('includes/functions.php');
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="<?php echo(link);?>js/jquery-1.9.1.js"></script>
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Panel</title>
    <link href="<?php echo(link);?>css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>


    <!-- Page Content -->
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h1 class="mt-5">A Bootstrap 4 Starter Template</h1>
          <p class="lead">Complete with pre-defined file paths and responsive navigation!</p>
          <?php
             if(isset($_GET['where'])) {
                 echo 'You searched for '. $_GET['where'];
             }
             ?>
      <p><a class="nav-link" href="<?php echo(link);?>loadone/graphics_and_design">Go to graphics and design</a></p>
		  <p><a class="nav-link" href="<?php echo(link);?>loadone">Go to All</a></p>
        </div>
      </div>
    </div>

	
	
    <div class="container">	
      <div class="row">
             <div class="col-md-3">
                <form method="post" id="searchForm">
                      <input type="search" id="sq" name="search">
                </form>
            </div>
            <div class="col-md-6"></div>
              <div class="col-md-3">
                    <select id="mySelect" onchange="selectFunc()" name="fields">
                        <option value="">Select One …</option>
                        <option value="graphics_and_design">graphics and design</option>
                        <option value="programming_and_tech">programming and tech</option>
                        <option value="video_and_animation">video and animation</option>
                        <option value="admin_support">admin support</option>
                        <option value="sales_and_marketing">sales and marketing</option>
                    </select>
               </div>

               <p id="demo"></p>
      </div>
      <br/><br/><br/>
    	
    	
    	<div class="col-lg-12" id="results"></div>	
    	<div id="loader_image">
    		<img src="loader.gif" alt="" width="50"> 
    	</div>
    	
    	<div class="margin10"></div>
    	<div id="loader_message"></div>
    </div>

<?php
$he = 'hello';
?>
	<script type="text/javascript">

    var inputFunc = function(event) {
      var input = document.getElementById("sq").value;
    // document.getElementById("write").innerHTML = input;
          window.location.href = '<?php echo(link);?>loadone/' + input;
    event.preventDefault();
  };

  var form = document.getElementById("searchForm");

  form.addEventListener("submit", inputFunc, true);

      function selectFunc() {
      var x = document.getElementById("mySelect").value;
      window.location.href = '<?php echo(link);?>loadone/' + x;
      // document.getElementById("demo").innerHTML = "You selected: " + x;
  }

      var busy = false;
      var limit = 5
      var offset = 0;
      var where = '<?php 
                      if(isset($_GET["where"])) {
                         echo $_REQUEST['where'];
                       } else {
                           echo ' ';
                       }
                         ?>';


      function displayRecords(lim, off, where) {
        $.ajax({
          type: "GET",
          async: false,
          url: "<?php echo(link);?>getrecords.php",
          data: "limit=" + lim + "&offset=" + off + "&where=" + where,
          cache: false,
          beforeSend: function() {
            $("#loader_message").html("").hide();
            $('#loader_image').show();
          },
          success: function(html) {
            $("#results").append(html);
            $('#loader_image').hide();
            if (html == "") {
              $("#loader_message").html('<button class="btn btn-default" type="button">No more records.</button>').show()
            } else {
              // $("#loader_message").html('<button class="btn btn-default" type="button">Loading please wait...</button>').show();
              $('#loader_image').show();

            }
            window.busy = false;


          }
        });
      }

      $(document).ready(function() {
        // start to load the first set of data
        if (busy == false) {
          busy = true;
          // start to load the first set of data
          displayRecords(limit, offset, where);
        }


        $(window).scroll(function() {
          // make sure u give the container id of the data to be loaded in.
          if ($(window).scrollTop() + $(window).height() > $("#results").height() && !busy) {
            busy = true;
            offset = limit + offset;
            where = '<?php 
                      if(isset($_GET["where"])) {
                         echo $_REQUEST['where'];
                       } else {
                           echo ' ';
                       }
                         ?>';

            // this is optional just to delay the loading of data
            setTimeout(function() { displayRecords(limit, offset, where); }, 500);

            // you can remove the above code and can use directly this function
            // displayRecords(limit, offset);

          }
        });

      });
    </script>
	
  </body>
</html>
