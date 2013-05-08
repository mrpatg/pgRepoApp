<?php include('header.php'); 


if($_SESSION['auth'] == TRUE){
?>


       <div class="content">
        <div class="page-header">
          <h1>Main Screen <small>This is pretty much it.</small></h1>
        </div>
        <div class="row">
          <div class="span10">
            <h2>Main content</h2>
			<?php if($_GET['msg'] == "success"){ echo '<span class="label">Upload Sucessful!</span>'; } ?>
			<?php if($_GET['msg'] == "fail"){ echo '<span class="label warning">Upload Failed!</span>'; } ?>
			<?php if($_GET['msg'] == "delsuccess"){ echo '<span class="label">Deletion Sucessful!</span>'; } ?>
			<?php if($_GET['msg'] == "delfail"){ echo '<span class="label warning">Deletion Failed!</span>'; } ?>
			<h4>Total link count <?php total_link_count(); ?></h4>
			<ul class="unstyled">
				<?php show_repo(); ?>
			</ul>
          </div>
          <div class="span4">
            <h3>Upload New CSV</h3>
			<form method="POST" class="form-stacked" action="upload.php" enctype="multipart/form-data">
				<p><input type="file" name="csv"></p>
				<button class="btn success" type="submit">Upload</button>
			</form>
          </div>
        </div>
      </div>

<? } else { ?>

       <div class="content">
        <div class="row">
          <div class="span10">
            <h2>Welcome</h2>

			<p>Please login to use the app.</p>
          </div>
          <div class="span4">
            <? if($_SESSION['autherr'] == TRUE && $_SESSION['auth'] == FALSE){ ?>
			<div class="alert-message error">
		        <a class="close" href="#">×</a>
		        <p><strong>Oh snap!</strong> Login didnt work, try again.</p>
		      </div>
			<? } ?>
          </div>
        </div>
      </div>



<? }

include('footer.php'); 


?>