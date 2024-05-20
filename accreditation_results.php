<?php
include("config.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch accreditation reports
$reportsResult = $conn->query("SELECT * FROM accreditation_reports");

// Close connection
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accreditation Reports</title>
</head>
<body>
    <h1>Accreditation Reports</h1>
    <table border="1">
        <tr>
            <th>ReportID</th>
            <th>VisitID</th>
            <th>Generated Date</th>
            <th>File URL</th>
        </tr>
        <?php while ($report = $reportsResult->fetch_assoc()) : ?>
            <tr>
                <td><?= $report['ReportID'] ?></td>
                <td><?= $report['VisitID'] ?></td>
                <td><?= $report['GeneratedDate'] ?></td>
                <td><?= $report['FileURL'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>