<?php
include('config.php');

header('Content-Type: application/json');

if (isset($_GET['programId'])) {
    $programId = $_GET['programId'];

    // SQL query to fetch accreditation visits for the given program ID, including the fileURL from the accreditation_reports table
    $query = "SELECT accreditationvisits.VisitDate, accreditationvisits.VisitType, accreditationvisits.AssessmentTeam, accreditationvisits.VisitOutcome, accreditationvisits.Comments, accreditation_reports.fileURL
              FROM accreditationvisits
              LEFT JOIN accreditation_reports ON accreditationvisits.VisitID = accreditation_reports.VisitID
              WHERE accreditationvisits.ProgramID = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $programId);
        $stmt->execute();
        $stmt->bind_result($visitDate, $visitType, $assessmentTeam, $visitOutcome, $comments, $fileURL);

        $visits = array();

        while ($stmt->fetch()) {
            $visits[] = array(
                'date' => $visitDate,
                'type' => $visitType,
                'assessmentTeam' => $assessmentTeam,
                'outcome' => $visitOutcome,
                'comments' => $comments,
                'fileURL' => $fileURL // Add the fileURL to the visits array
            );
        }

        $stmt->close();
    } else {
        echo json_encode(array('error' => 'Failed to prepare statement'));
        exit;
    }
    $conn->close();

    // Return the data as JSON
    echo json_encode($visits);
} else {
    echo json_encode(array('error' => 'Program ID not provided'));
}
?>
