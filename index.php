<?php

include "config.php";

/* Get file counts */

$total = 0;
$unchanged = 0;
$modified = 0;
$added = 0;
$deleted = 0;

$result = $conn->query(
    "SELECT status, COUNT(*) AS count
     FROM files
     GROUP BY status"
);

while ($row = $result->fetch_assoc()) {

    switch ($row["status"]) {

        case "UNCHANGED":
            $unchanged = $row["count"];
            break;

        case "MODIFIED":
            $modified = $row["count"];
            break;

        case "ADDED":
            $added = $row["count"];
            break;

        case "DELETED":
            $deleted = $row["count"];
            break;
    }
}


/* Get total number of files */

$total_result = $conn->query(
    "SELECT COUNT(*) AS total FROM files"
);

$total = $total_result->fetch_assoc()["total"];


/* Get last scan time */

$last_scan_result = $conn->query(
    "SELECT scan_time
     FROM scan_logs
     ORDER BY id DESC
     LIMIT 1"
);

$last_scan = "No scans yet";

if ($last_scan_result->num_rows > 0) {

    $last_scan_data = $last_scan_result->fetch_assoc();

    $last_scan = $last_scan_data["scan_time"];
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>File Integrity Dashboard</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body>

<div class="container">


    <!-- HEADER -->

    <header class="header">

        <h1>File Integrity Dashboard</h1>

        <p>
            SHA-256 Based File Integrity Monitoring System
        </p>

    </header>


    <!-- STATISTICS -->

    <div class="cards">

        <div class="card">

            <h3>Total Files</h3>

            <p>
                <?php echo $total; ?>
            </p>

        </div>


        <div class="card">

            <h3>Unchanged</h3>

            <p>
                <?php echo $unchanged; ?>
            </p>

        </div>


        <div class="card">

            <h3>Modified</h3>

            <p>
                <?php echo $modified; ?>
            </p>

        </div>


        <div class="card">

            <h3>Added</h3>

            <p>
                <?php echo $added; ?>
            </p>

        </div>


        <div class="card">

            <h3>Deleted</h3>

            <p>
                <?php echo $deleted; ?>
            </p>

        </div>

    </div>


    <!-- LAST SCAN -->

    <div class="panel">

        <h2>Last Scan</h2>

        <p>

            Last scan performed:

            <strong>
                <?php echo htmlspecialchars($last_scan); ?>
            </strong>

        </p>

    </div>


    <!-- SCAN FORM -->

    <div class="panel">

        <h2>Start File Scan</h2>

        <form
            action="scan.php"
            method="POST"
            onsubmit="return confirmScan()"
        >

            <label for="directory">
                Directory to Monitor
            </label>

            <input
                type="text"
                id="directory"
                name="directory"
                value="monitored"
                required
            >

            <button type="submit">
                Start Scan
            </button>

        </form>

    </div>


    <!-- MONITORED FILES -->

    <div class="panel">

        <h2>Monitored Files</h2>


        <!-- SEARCH AND FILTER -->

        <div class="filters">

            <input
                type="text"
                id="searchInput"
                placeholder="Search files..."
                onkeyup="filterFiles()"
            >


            <select
                id="statusFilter"
                onchange="filterFiles()"
            >

                <option value="ALL">
                    All Status
                </option>

                <option value="UNCHANGED">
                    Unchanged
                </option>

                <option value="MODIFIED">
                    Modified
                </option>

                <option value="ADDED">
                    Added
                </option>

                <option value="DELETED">
                    Deleted
                </option>

            </select>

        </div>


        <!-- FILE TABLE -->

        <table id="fileTable">

            <thead>

                <tr>

                    <th>Filename</th>

                    <th>Path</th>

                    <th>SHA-256</th>

                    <th>Status</th>

                </tr>

            </thead>


            <tbody>

            <?php

            $files = $conn->query(
                "SELECT * FROM files
                 ORDER BY id DESC"
            );

            while ($file = $files->fetch_assoc()) {

            ?>

                <tr>

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

                        <span class="status
                            <?php

                            echo strtolower(
                                $file["status"]
                            );

                            ?>">

                            <?php

                            echo htmlspecialchars(
                                $file["status"]
                            );

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
            Dashboard
        </a>

        <a href="files.php">
            Detailed File Information
        </a>

        <a href="logs.php">
            Scan Logs
        </a>

    </div>

</div>


<script src="js/j.js"></script>

</body>

</html>