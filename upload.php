<?php 
    require_once("./views/header.php");
	require_once("./views/classes/VideoDetailsFormProvider.php");
	
	if(!User::isLoggedIn()){
		header("Location: signIn.php");
	}
?>

    <div class = "column">
        
        <?php 
            $formProvider = new VideoDetailsFormProvider($conn);
            echo $formProvider->createUploadForm();
        ?>
    </div>

	<script >
		$("form").submit(function(){

			$("#loadingModal").modal("show");
		});
	</script>

    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
      
      			<div class="modal-body">
					This might take a while .Please wait...
					<img src='assets/images/icons/loading-spinner.gif'>
      			</div>
      
    		</div>
  		</div>
	</div>

<?php require_once("./views/footer.php");?>