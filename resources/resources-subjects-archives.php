<?php
include "../dbconnect.php";
OpenSession();
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;;
$subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/resources.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>

    <div id="blur" onclick="closeUploadForm()"></div>

    <p class="title-resources">RESOURCES</p>

    <?php
    $row_subject_info = SelectSubjectRecord($subject_id);
    echo "<P class='subject-name'>" . (($row_subject_info != null) ? $row_subject_info['subject_name'] : "GENERAL") . "</P>";
    ?>

    <div class="body-2">
        <div class="table-case">

            <!-- Table for the resources -->
            <table class="table">
                <tbody class="table-body">
                    <tr>
                        <th class="heading">Date</th>
                        <th class="heading">Task</th>
                        <th class="heading">Link</th>
                        <th class="heading">Description</th>
                    </tr>

                    <div class="case">
                        <?php
                        if (isset($_GET['subject_id'])) {
                            $note = GetResourcesFromSubject($_GET['subject_id']);
                            while ($note_row = $note->fetch_assoc()) {       ?>
                                <tr>
                                    <td><?php echo $note_row['due_date']; ?></td>
                                    <td><?php echo $note_row['note_title']; ?></td>
                                    <td class="filename">
                                        <a href="<?php echo $note_row['link']; ?>" id="custom-link" target="_blank">
                                            <?php echo (strlen($note_row['link']) > 50) ? substr($note_row['link'], 0, 50) . "..." : $note_row['link']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $note_row['description']; ?></td>
                                </tr>
                            <?php }
                        } elseif (isset($_GET['class_id'])) {
                            $note = GetResourcesFromGeneral($_GET['class_id']);
                            while ($note_row = $note->fetch_assoc()) {       ?>
                                <tr>
                                    <td><?php echo $note_row['due_date']; ?></td>
                                    <td><?php echo $note_row['note_title']; ?></td>
                                    <td class="filename">
                                        <a href="<?php echo $note_row['link']; ?>" id="custom-link" target="_blank">
                                            <?php echo substr($note_row['link'], 0, 50); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $note_row['description']; ?></td>
                                </tr>
                        <?php }
                        } ?>
                    </div>

                </tbody>
            </table>
        </div>
    </div>
</body>

</html>