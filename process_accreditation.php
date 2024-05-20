<?php
include("config.php");
require('fpdf186/fpdf.php');

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $program = $_POST['program'];
    $visit_date = $_POST['visit_date'];
    $visit_type = $_POST['visit_type'];
    $assessment_team = $_POST['assessment_team'];
    $visit_outcome = $_POST['visit_outcome'];
    $overall_comments = $_POST['overall_comments'];
    $scores = $_POST['scores'];
    $comments = isset($_POST['comments']) ? $_POST['comments'] : array(); // Check if comments key exists

    // Prepare and bind SQL statement for inserting accreditation visits
    $stmt = $conn->prepare("INSERT INTO accreditationvisits (ProgramID, VisitDate, VisitType, AssessmentTeam, VisitOutcome, Comments, Status) VALUES (?, ?, ?, ?, ?, ?, 'Completed')");
    $stmt->bind_param("isssss", $program, $visit_date, $visit_type, $assessment_team, $visit_outcome, $overall_comments);

    // Execute SQL statement for inserting accreditation visits
    $stmt->execute();

    // Get the last inserted VisitID
    $visitID = $stmt->insert_id;

    // Close statement
    $stmt->close();

    // Insert scores and comments into the accreditation_scores table
    foreach ($scores as $criterionID => $score) {
        $comment = isset($comments[$criterionID]) ? $comments[$criterionID] : ''; // Get comment based on CriterionID

        // Prepare and bind SQL statement for inserting accreditation scores
        $scoreStmt = $conn->prepare("INSERT INTO accreditation_scores (CriterionID, VisitID, Score, Comments) VALUES (?, ?, ?, ?)");
        $scoreStmt->bind_param("iiis", $criterionID, $visitID, $score, $comment);

        // Execute SQL statement for inserting accreditation scores
        $scoreStmt->execute();

        // Close statement
        $scoreStmt->close();
    }

    // Calculate total sum of accreditation scores for the program
    $totalScore = array_sum($scores);

    // Determine AccreditationLevel based on total sum
    if ($totalScore > 22) {
        $accreditationLevel = 'Level III';
    } elseif ($totalScore > 18) {
        $accreditationLevel = 'Level II';
    } else {
        $accreditationLevel = 'Level I';
    }
    
    // Update AccreditationStatus and AccreditationLevel for the program
    $updateStmt = $conn->prepare("UPDATE programs SET AccreditationStatus = 'Accredited', AccreditationLevel = ? WHERE ProgramID = ?");
    $updateStmt->bind_param("si", $accreditationLevel, $program);
    $updateStmt->execute();
    $updateStmt->close();

    // Generate PDF Report
    $pdf = new FPDF('L'); // Set landscape orientation
    $pdf->AddPage();
    $pdf->SetFont('Times', 'B', 16); // Use Times New Roman font
    $pdf->Cell(0, 10, 'Accreditation Visit Report', 0, 1, 'C');
    $pdf->Ln(10);

    // Add table headers
    $pdf->SetFont('Times', 'B', 12); // Use Times New Roman font
    $pdf->Cell(115, 10, 'Program', 1);
    $pdf->Cell(30, 10, 'Visit Date', 1);
    $pdf->Cell(55, 10, 'Visit Type', 1);
    $pdf->Cell(50, 10, 'Visit Outcome', 1);
    $pdf->Cell(25, 10, 'Score', 1);
    $pdf->Ln();

    // Fetch the program name
    $programNameQuery = "SELECT Name FROM programs WHERE ProgramID = ?";
    $programStmt = $conn->prepare($programNameQuery);
    $programStmt->bind_param("i", $program);
    $programStmt->execute();
    $programResult = $programStmt->get_result();
    $programRow = $programResult->fetch_assoc();
    $programName = $programRow['Name'];
    $programStmt->close();

    // Output accreditation visit data in the PDF
    $pdf->SetFont('Times', '', 12); // Use Times New Roman font
    $pdf->Cell(115, 10, $programName, 1);
    $pdf->Cell(30, 10, $visit_date, 1);
    $pdf->Cell(55, 10, $visit_type, 1);
    $pdf->Cell(50, 10, $visit_outcome, 1);
    $pdf->Cell(25, 10, $totalScore, 1); // Total Score
    $pdf->Ln();

    // Output comments and scores for the accreditation criteria
    $pdf->SetFont('Times', 'B', 12); // Use Times New Roman font
    $pdf->Cell(0, 10, 'Accreditation Criteria', 0, 1);
    $pdf->SetFont('Times', 'B', 12); // Use Times New Roman font
    $pdf->Cell(70, 10, 'Criterion Name', 1);
    $pdf->Cell(130, 10, 'Description', 1);
    $pdf->Cell(25, 10, 'Score', 1);
    $pdf->Cell(50, 10, 'Comment', 1);
    $pdf->Ln();

    $pdf->SetFont('Times', '', 12); // Use Times New Roman font
    foreach ($scores as $criterionID => $score) {
        // Fetch criterion name and description
        $criterionQuery = "SELECT criterionName, Description FROM accreditation_criteria WHERE CriterionID = ?";
        $criterionStmt = $conn->prepare($criterionQuery);
        $criterionStmt->bind_param("i", $criterionID);
        $criterionStmt->execute();
        $criterionResult = $criterionStmt->get_result();
        $criterionRow = $criterionResult->fetch_assoc();
        $criterionName = $criterionRow['criterionName'];
        $criterionDescription = $criterionRow['Description'];
        $criterionStmt->close();

        $comment = isset($comments[$criterionID]) ? $comments[$criterionID] : '';
        $pdf->Cell(70, 10, $criterionName, 1);
        $pdf->Cell(130, 10, $criterionDescription, 1);
        $pdf->Cell(25, 10, $score, 1);
        $pdf->Cell(50, 10, $comment, 1);
        $pdf->Ln();
    }

    // Save the generated PDF file
    $pdfFilePath = 'reports/accreditation_visit_report_' . $visitID . '.pdf';
    $pdf->Output($pdfFilePath, 'F');

    // Insert the PDF file path into the accreditation_reports table
    $insertReportSql = "INSERT INTO accreditation_reports (VisitID, FileURL) VALUES (?, ?)";
    $insertReportStmt = $conn->prepare($insertReportSql);
    $insertReportStmt->bind_param('is', $visitID, $pdfFilePath);
    if ($insertReportStmt->execute()) {
        echo "PDF link inserted into the accreditation_reports table successfully.";
    } else {
        echo "Error inserting PDF link: " . $conn->error;
    }
    $insertReportStmt->close();

    // Close connection
    $conn->close();

    // Redirect to success page or perform other actions
    header("Location: accreditation_form.php");
    exit();
}
?>
