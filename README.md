# File Integrity Monitoring Dashboard

## Overview

A cybersecurity project that monitors files and detects integrity changes using SHA-256 cryptographic hashing.

## Features

- File and directory scanning
- SHA-256 hash calculation
- Baseline creation and comparison
- Detection of added, modified, and deleted files
- Web dashboard for monitoring results
- PHP and MySQL integration

## Technologies

- Python
- HTML and CSS
- JavaScript
- PHP
- MySQL
- XAMPP

## How It Works

1. Scan the monitored directory.
2. Calculate SHA-256 hashes for files.
3. Store a trusted baseline.
4. Compare current hashes against the baseline.
5. Detect added, modified, and deleted files.
6. Display the results on the dashboard.

## Setup

1. Install XAMPP with Apache and MySQL.
2. Place the project in `C:\xampp\htdocs\`.
3. Create the required MySQL database and tables using the project's SQL setup instructions.
4. Configure the database connection locally.
5. Start Apache and MySQL.
6. Run the Python monitoring component according to the project's instructions.
7. Open `http://localhost/your-project-folder/` in your browser.

## Security

Use this project only to monitor files you are authorized to access. Do not publish credentials or sensitive monitoring data.

## Author

Your Name
