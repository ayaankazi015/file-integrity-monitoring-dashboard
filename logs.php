<?php

include "config.php";

/* Get scan history */

$result = $conn->query(
    "SELECT * FROM scan_logs
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Scan Logs</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body>

<div class="container">

    <!-- HEADER -->

    <header class="header">

        <h1>Scan Logs</h1>

        <p>
            History of File Integrity Scans
        </p>

    </header>


    <!-- LOG TABLE -->

    <div class="panel">

        <h2>Scan History</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Scan Time</th>

                    <th>Total Files</th>

                    <th>Added</th>

                    <th>Modified</th>

                    <th>Deleted</th>

                    <th>Unchanged</th>

                </tr>

            </thead>


            <tbody>

            <?php

            while ($log = $result->fetch_assoc()) {

            ?>

                <tr>

                    <td>
                        <?php echo $log["id"]; ?>
                    </td>

                    <td>
                        <?php echo $log["scan_time"]; ?>
                    </td>

                    <td>
                        <?php echo $log["total_files"]; ?>
                    </td>

                    <td>
                        <?php echo $log["added_files"]; ?>
                    </td>

                    <td>
                        <?php echo $log["modified_files"]; ?>
                    </td>

                    <td>
                        <?php echo $log["deleted_files"]; ?>
                    </td>

                    <td>
                        <?php echo $log["unchanged_files"]; ?>
                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>


    <!-- BACK BUTTON -->

    <div class="navigation">

        <a href="index.php">
            Back to Dashboard
        </a>

        <a href="files.php">
            Detailed File Information
        </a>

    </div>

</div>

</body>

</html>