<script>
function abc(){
    document.upload_form.submit();
}
</script>
<div class="mb-3">
<form name="upload_form" action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="hidden" value="Upload Image" class="btn btn-sm btn-secondary" name="submit_test" onclick="abc();">  
    <button onclick="abc();" type="button">aaa</button>
</form>
<!-- <form name="upload_form" action="upload1.php" method="post">
    <input type="text" name="fileToUpload" id="fileToUpload" value="abc">
    <input type="text" value="Upload Image" class="btn btn-sm btn-secondary" name="submit_test1" >  
    <button onclick="abc();" type="button">aaa</button>
</form> -->
</div>


