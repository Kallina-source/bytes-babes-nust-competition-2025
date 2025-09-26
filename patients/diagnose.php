<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$symptoms = $_POST['symptoms'] ?? [];

// A basic rule-based diagnosis engine based on the provided document
function getDiagnosis($symptoms) {
    // Convert symptoms array to a string for easier checking
    $symptoms_string = implode(',', $symptoms);

    // Rule for Malaria
    $malaria_symptoms_vs = ['Abdominal pain', 'Vomiting', 'sore throat'];
    $malaria_symptoms_s = ['Headache', 'Fatigue', 'Cough', 'Constipation'];
    $malaria_symptoms_w = ['Chest pain', 'Back pain', 'Muscle Pain'];
    $malaria_symptoms_vw = ['Diarrhea', 'sweating', 'rash', 'Loss of appetite'];

    $is_malaria_vs = !empty(array_intersect($symptoms, $malaria_symptoms_vs));
    $is_malaria_s = !empty(array_intersect($symptoms, $malaria_symptoms_s));
    $is_malaria_w = !empty(array_intersect($symptoms, $malaria_symptoms_w));
    $is_malaria_vw = !empty(array_intersect($symptoms, $malaria_symptoms_vw));

    // Rule for Typhoid Fever
    $typhoid_symptoms_vs = ['Abdominal pain', 'Stomach issues'];
    $typhoid_symptoms_s = ['Headache', 'Persistent high fever'];
    $typhoid_symptoms_w = ['Weakness', 'Tiredness'];
    $typhoid_symptoms_vw = ['Rash', 'Loss of appetite'];

    $is_typhoid_vs = !empty(array_intersect($symptoms, $typhoid_symptoms_vs));
    $is_typhoid_s = !empty(array_intersect($symptoms, $typhoid_symptoms_s));
    $is_typhoid_w = !empty(array_intersect($symptoms, $typhoid_symptoms_w));
    $is_typhoid_vw = !empty(array_intersect($symptoms, $typhoid_symptoms_vw));

    // Combined or Complex Diagnoses
    if ($is_malaria_vs && $is_typhoid_vs) {
        return [
            'success' => true,
            'diagnosis' => 'Possible Malaria and Typhoid Co-infection',
            'message' => 'Based on the presence of very strong signs for both Malaria and Typhoid, a co-infection is possible. Immediate medical attention is required.',
            'recommendations' => [
                'Seek Medical Confirmation: Consult a doctor for a definitive diagnosis and treatment plan.',
                'Chest X-ray: The presence of very strong signs requires a chest X-ray in addition to drug administration.'
            ]
        ];
    }
    
    // Check for Malarial Diagnosis based on symptom strength
    if ($is_malaria_vs || $is_malaria_s || $is_malaria_w || $is_malaria_vw) {
        $recommendations = [
            'Seek Medical Confirmation: This is a preliminary analysis. Please consult a doctor for a definitive diagnosis.',
            'Medication: Do not self-medicate. A doctor will prescribe the correct medication (e.g., Coartem) after confirmation.'
        ];
        if ($is_malaria_vs) {
            $recommendations[] = 'Chest X-ray: The presence of very strong signs (e.g., Abdominal pain, Vomiting) requires a chest X-ray in addition to drug administration.';
        }
        return [
            'success' => true,
            'diagnosis' => 'Likely Malaria',
            'message' => 'Based on your selected symptoms, the system indicates a high probability of Malaria.',
            'recommendations' => $recommendations
        ];
    }

    // Check for Typhoid Diagnosis based on symptom strength
    if ($is_typhoid_vs || $is_typhoid_s || $is_typhoid_w || $is_typhoid_vw) {
        $recommendations = [
            'Seek Medical Confirmation: This is a preliminary analysis. Please consult a doctor for a definitive diagnosis.',
            'Medication: Do not self-medicate. A doctor will prescribe the correct medication (e.g., Ciprofloxacin, Azithromycin) after confirmation.'
        ];
        if ($is_typhoid_vs) {
            $recommendations[] = 'Chest X-ray: The presence of very strong signs (e.g., Abdominal pain, Stomach issues) requires a chest X-ray in addition to drug administration.';
        }
        return [
            'success' => true,
            'diagnosis' => 'Likely Typhoid Fever',
            'message' => 'Based on your selected symptoms, the system indicates a high probability of Typhoid Fever.',
            'recommendations' => $recommendations
        ];
    }
    
    // Added rule for Tuberculosis
    if (in_array('Dry Cough', $symptoms) && in_array('Fatigue', $symptoms) && in_array('Fever', $symptoms)) {
        return [
            'success' => true,
            'diagnosis' => 'Possible Tuberculosis (TB)',
            'message' => 'Based on a combination of Dry Cough, Fatigue, and Fever, you may have Tuberculosis. This requires immediate medical confirmation.',
            'recommendations' => [
                'Seek Immediate Medical Attention: A positive diagnosis requires a medical professional to perform tests and prescribe a long-term treatment plan.',
                'Consult a Doctor: Only a doctor can confirm the diagnosis and prescribe the correct medication.'
            ]
        ];
    }
    
    // Added rule for Diabetes
    if (in_array('Weakness', $symptoms) && in_array('Fatigue', $symptoms) && in_array('Thirst', $symptoms)) { // Note: 'Thirst' is not in the original list, but this is an example for expansion
        return [
            'success' => true,
            'diagnosis' => 'Possible Diabetes',
            'message' => 'Based on a combination of Weakness, Fatigue, and excessive Thirst, the system suggests a possibility of Diabetes. Please note that this is a preliminary analysis.',
            'recommendations' => [
                'Consult a Doctor: A blood test is required for a definitive diagnosis.',
                'Medication and Lifestyle: Treatment often involves lifestyle changes, diet, and medication prescribed by a professional.'
            ]
        ];
    }

    // Default response if no specific rules are matched
    return [
        'success' => true,
        'diagnosis' => 'Further Evaluation Needed',
        'message' => 'Based on the selected symptoms, the system cannot provide a specific diagnosis. We recommend you consult a doctor for a professional evaluation.',
        'recommendations' => [
            'Book an appointment with a doctor for a thorough examination.'
        ]
    ];
}

$diagnosis_result = getDiagnosis($symptoms);
echo json_encode($diagnosis_result);
?>