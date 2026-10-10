<?php

include "config.php";

/*
|--------------------------------------------------------------------------
| Directory to monitor
|--------------------------------------------------------------------------
*/

$directory = __DIR__ . "/monitored/";


/*
|--------------------------------------------------------------------------
| Check directory
|--------------------------------------------------------------------------
*/

if (!is_dir($directory)) {
    die("Monitored directory does not exist.");
}


/*
|--------------------------------------------------------------------------
| Counters
|--------------------------------------------------------------------------
*/

$added = 0;
$modified = 0;
$unchanged = 0;
$deleted = 0;

$scanned_files = [];


/*
|--------------------------------------------------------------------------
| Scan monitored directory
|--------------------------------------------------------------------------
*/

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);


foreach ($iterator as $file) {

    if ($file->isFile()) {

        $full_path = $file->getPathname();


        /*
        |--------------------------------------------------------------------------
        | Relative path
        |--------------------------------------------------------------------------
        */

        $relative_path = str_replace(
            $directory,
            "",
            $full_path
        );


        $filename = $file->getFilename();


        /*
        |--------------------------------------------------------------------------
        | Calculate current SHA-256
        |--------------------------------------------------------------------------
        */

        $current_hash = hash_file(
            "sha256",
            $full_path
        );


        $file_size = filesize($full_path);


        $last_modified = date(
            "Y-m-d H:i:s",
            filemtime($full_path)
        );


        $scanned_files[] = $relative_path;


        /*
        |--------------------------------------------------------------------------
        | Check database
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "SELECT * FROM files
             WHERE filepath = ?"
        );

        $stmt->bind_param(
            "s",
            $relative_path
        );

        $stmt->execute();

        $result = $stmt->get_result();


        /*
        |--------------------------------------------------------------------------
        | New file
        |--------------------------------------------------------------------------
        */

        if ($result->num_rows == 0) {

            $status = "ADDED";


            /*
            | For a new file, its current hash becomes
            | the initial baseline hash.
            */

            $baseline_hash = $current_hash;


            $insert = $conn->prepare(
                "INSERT INTO files
                (
                    filename,
                    filepath,
                    sha256_hash,
                    baseline_hash,
                    file_size,
                    last_modified,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );


            $insert->bind_param(
                "ssssiss",
                $filename,
                $relative_path,
                $current_hash,
                $baseline_hash,
                $file_size,
                $last_modified,
                $status
            );


            $insert->execute();


            $added++;

        }


        /*
        |--------------------------------------------------------------------------
        | Existing file
        |--------------------------------------------------------------------------
        */

        else {

            $old_file = $result->fetch_assoc();


            /*
            |--------------------------------------------------------------------------
            | Get trusted baseline
            |--------------------------------------------------------------------------
            */

            $baseline_hash = $old_file["baseline_hash"];


            /*
            |--------------------------------------------------------------------------
            | Compare current hash with baseline
            |--------------------------------------------------------------------------
            */

            if ($current_hash === $baseline_hash) {

                $status = "UNCHANGED";

                $unchanged++;

            }

            else {

                $status = "MODIFIED";

                $modified++;

            }


            /*
            |--------------------------------------------------------------------------
            | Update current information
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | baseline_hash is NOT changed.
            |
            */

            $update = $conn->prepare(
                "UPDATE files
                 SET filename = ?,
                     sha256_hash = ?,
                     file_size = ?,
                     last_modified = ?,
                     status = ?
                 WHERE filepath = ?"
            );


            $update->bind_param(
                "ssisss",
                $filename,
                $current_hash,
                $file_size,
                $last_modified,
                $status,
                $relative_path
            );


            $update->execute();

        }

    }

}


/*
|--------------------------------------------------------------------------
| Detect deleted files
|--------------------------------------------------------------------------
*/

$result = $conn->query(
    "SELECT filepath FROM files"
);


while ($row = $result->fetch_assoc()) {

    if (!in_array(
        $row["filepath"],
        $scanned_files
    )) {

        $status = "DELETED";


        $update = $conn->prepare(
            "UPDATE files
             SET status = ?
             WHERE filepath = ?"
        );


        $update->bind_param(
            "ss",
            $status,
            $row["filepath"]
        );


        $update->execute();


        $deleted++;

    }

}


/*
|--------------------------------------------------------------------------
| Save scan information
|--------------------------------------------------------------------------
*/

$total_files = count($scanned_files);


$log = $conn->prepare(
    "INSERT INTO scan_logs
    (
        total_files,
        added_files,
        modified_files,
        deleted_files,
        unchanged_files
    )
    VALUES (?, ?, ?, ?, ?)"
);


$log->bind_param(
    "iiiii",
    $total_files,
    $added,
    $modified,
    $deleted,
    $unchanged
);


$log->execute();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Scan Result</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body>

<div class="container">

    <div class="scan-box">

        <h1>File Integrity Scan Complete</h1>


        <p>
            Total Files:
            <strong>
                <?php echo $total_files; ?>
            </strong>
        </p>


        <p>
            Added:
            <strong>
                <?php echo $added; ?>
            </strong>
        </p>


        <p>
            Modified:
            <strong>
                <?php echo $modified; ?>
            </strong>
        </p>


        <p>
            Deleted:
            <strong>
                <?php echo $deleted; ?>
            </strong>
        </p>


        <p>
            Unchanged:
            <strong>
                <?php echo $unchanged; ?>
            </strong>
        </p>


        <br>


        <a href="index.php"
           class="button">

            Back to Dashboard

        </a>

    </div>

</div>

</body>

</html>