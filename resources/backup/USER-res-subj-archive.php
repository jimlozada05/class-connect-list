<!-- upload for user resources  -->
<div class="button-box">
    <button class="upload-btn" onclick="openUploadForm()">UPLOAD</button>
</div>

<!-- POPUP (UPLOAD FORM) -->
<div id="uploadpopup" class="popup">
    <form id="formUpload" class="form-container">
        <!-- <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>"> -->

        <p class="resource-header">UPLOAD RESOURCES</p>
        <div><label>Due date</label></div>
        <div><input type="date" class="form-control" name="due_date"></div>

        <label>Task</label>
        <input type="text" class="form-control" name="note_title" placeholder="Title" required>

        <label for="" class="resource-upload">Upload Resources</label>
        <input type="file" id="resource-file" name="filename" class="resource-file">

        <label>Description</label>
        <textarea class="form-control" name="description" style="height: 100px" placeholder="Note Description (optional)"></textarea>

        <!-- <div><label>Due Time</label></div>
            <div><input type="time" class="form-control" name="due_time"></div> -->

        <div><br><span class="form-text">Due date is optional!</span></div>

        <br><button type="submit" class="upload-btn">UPLOAD</button>
    </form>
</div>

<script type="text/javascript">
    function openUploadForm() {
        document.getElementById("uploadpopup").style.display = "block";
        document.getElementById('blur').style.filter = "blur(5px)";
        document.getElementById('blur').style.display = "block";
        // document.getElementById('scrll').style.overflow = "hidden";
    }

    function closeUploadForm() {
        document.getElementById("uploadpopup").style.display = "none";
        document.getElementById('blur').style.filter = "blur(0)";
        document.getElementById('blur').style.display = "none";
        // document.getElementById('scrll').style.overflow = "auto";
    }
</script>