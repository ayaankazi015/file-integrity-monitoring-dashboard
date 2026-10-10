<?php

include "config.php";

/* Get all monitored files */

$result = $conn->query(
    "SELECT * FROM files
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Detailed File Information</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body>

<div class="container">

    <!-- HEADER -->

    <header class="header">

        <h1>Detailed File Information</h1>

        <p>
            Complete information about monitored files
        </p>

    </header>


    <!-- FILE INFORMATION -->

    <div class="panel">

        <h2>Monitored Files</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Filename</th>

                    <th>Path</th>

                    <th>SHA-256 Hash</th>

                    <th>Size</th>

                    <th>Last Modified</th>

                    <th>Status</th>

                </tr>

            </thead>


            <tbody>

            <?php

            while ($file = $result->fetch_assoc()) {

            ?>

                <tr>

                    <td>
                        <?php echo $file["id"]; ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $file["filename"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $file["filepath"]
                        );
                        ?>
                    </td>


                    <td class="hash">

                        <?php
                        echo htmlspecialchars(
                            $file["sha256_hash"]
                        );
                        ?>

                    </td>


                    <td>

                        <?php
                        echo $file["file_size"];
                        ?>
                        bytes

                    </td>


                    <td>

                        <?php
                        echo $file["last_modified"];
                        ?>

                    </td>


                    <td>

                        <span class="status
                            <?php
                            echo strtolower(
                                $file["status"]
                            );
                            ?>">

                            <?php
                            echo $file["status"];
                            ?>

                        </span>

                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>


    <!-- NAVIGATION -->

    <div class="navigation">

        <a href="index.php">
            Back to Dashboard
        </a>

        <a href="logs.php">
            Scan Logs
        </a>

    </div>

</div>

</body>

</html>