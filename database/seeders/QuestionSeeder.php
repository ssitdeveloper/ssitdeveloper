<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Option;
use App\Models\Chapter;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get chapters
        $cbneatChapter = Chapter::where('name', 'CBNAAT & TruNAAT')->first();
        $clinicalChapter = Chapter::where('name', 'Clinical Presentation of TB')->first();
        $drDetectionChapter = Chapter::where('name', 'Rapid Drug Resistance Detection')->first();
        $mdrTreatmentChapter = Chapter::where('name', 'MDR-TB Treatment Regimens')->first();
        $newAgentsChapter = Chapter::where('name', 'New Anti-TB Agents')->first();
        $classificationChapter = Chapter::where('name', 'Classification of TB')->first();

        // Questions for CBNAAT & TruNAAT
        if ($cbneatChapter) {
            $q1 = Question::create([
                'chapter_id' => $cbneatChapter->id,
                'question_text' => 'Which tests are included under NAAT for TB diagnosis?',
                'difficulty_level' => 'easy',
                'type' => 'single_choice',
                'explanation' => 'NAAT stands for Nucleic Acid Amplification Test. Both CBNAAT (Cartridge-Based) and TruNAAT are rapid molecular tests that can detect TB and rifampicin resistance simultaneously.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q1->id, 'option_text' => 'CBNAAT and TruNAAT', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q1->id, 'option_text' => 'Sputum microscopy and culture', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q1->id, 'option_text' => 'Mantoux test and IGRA', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q1->id, 'option_text' => 'X-ray and CT scan', 'is_correct' => false, 'order_by' => 4]);

            $q2 = Question::create([
                'chapter_id' => $cbneatChapter->id,
                'question_text' => 'What does CBNAAT stand for?',
                'difficulty_level' => 'easy',
                'type' => 'single_choice',
                'explanation' => 'CBNAAT = Cartridge-Based Nucleic Acid Amplification Test. It is a WHO-recommended rapid test that can detect both TB and rifampicin resistance in 2 hours.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q2->id, 'option_text' => 'Cartridge-Based Nucleic Acid Amplification Test', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q2->id, 'option_text' => 'Culture-Based Nucleic Acid Amplification Test', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q2->id, 'option_text' => 'Clinical-Based Nucleic Acid Amplification Test', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q2->id, 'option_text' => 'Centrifuge-Based Nucleic Acid Amplification Test', 'is_correct' => false, 'order_by' => 4]);

            $q3 = Question::create([
                'chapter_id' => $cbneatChapter->id,
                'question_text' => 'Which test rapidly detects rifampicin resistance in TB?',
                'difficulty_level' => 'easy',
                'type' => 'single_choice',
                'explanation' => 'CBNAAT can detect TB and simultaneously identify rifampicin resistance in 2 hours, making it the gold standard for rapid drug resistance detection.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q3->id, 'option_text' => 'CBNAAT', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q3->id, 'option_text' => 'Sputum smear microscopy', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q3->id, 'option_text' => 'Tuberculin skin test', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q3->id, 'option_text' => 'Chest X-ray', 'is_correct' => false, 'order_by' => 4]);

            $q4 = Question::create([
                'chapter_id' => $cbneatChapter->id,
                'question_text' => 'What is the time taken by CBNAAT to detect TB and rifampicin resistance?',
                'difficulty_level' => 'medium',
                'type' => 'single_choice',
                'explanation' => 'CBNAAT typically takes 2 hours to provide results for both TB detection and rifampicin resistance status, making it significantly faster than culture methods.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q4->id, 'option_text' => '2 hours', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q4->id, 'option_text' => '30 minutes', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q4->id, 'option_text' => '24 hours', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q4->id, 'option_text' => '1 week', 'is_correct' => false, 'order_by' => 4]);
        }

        // Clinical Presentation Questions
        if ($clinicalChapter) {
            $q5 = Question::create([
                'chapter_id' => $clinicalChapter->id,
                'question_text' => 'Which of the following is NOT a cardinal symptom of pulmonary TB?',
                'difficulty_level' => 'easy',
                'type' => 'single_choice',
                'explanation' => 'The cardinal symptoms of TB are: cough (>3 weeks), fever, night sweats, and weight loss. Chest pain can occur but is not a cardinal symptom.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q5->id, 'option_text' => 'Chest pain', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q5->id, 'option_text' => 'Persistent cough', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q5->id, 'option_text' => 'Night sweats', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q5->id, 'option_text' => 'Weight loss', 'is_correct' => false, 'order_by' => 4]);

            $q6 = Question::create([
                'chapter_id' => $clinicalChapter->id,
                'question_text' => 'What is the typical duration of cough in TB that should raise suspicion?',
                'difficulty_level' => 'easy',
                'type' => 'single_choice',
                'explanation' => 'A persistent cough lasting more than 3 weeks is considered a key symptom and should raise suspicion for TB, leading to further investigation.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q6->id, 'option_text' => 'More than 3 weeks', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q6->id, 'option_text' => '1-2 weeks', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q6->id, 'option_text' => 'More than 7 days', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q6->id, 'option_text' => '2-3 weeks', 'is_correct' => false, 'order_by' => 4]);
        }

        // Drug Resistance Detection
        if ($drDetectionChapter) {
            $q7 = Question::create([
                'chapter_id' => $drDetectionChapter->id,
                'question_text' => 'Which investigation is the gold standard for DR-TB diagnosis?',
                'difficulty_level' => 'medium',
                'type' => 'single_choice',
                'explanation' => 'Culture and drug susceptibility testing (DST) remain the gold standard for TB diagnosis and determining drug resistance. However, CBNAAT is now the recommended initial test.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q7->id, 'option_text' => 'Culture and drug susceptibility testing (DST)', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q7->id, 'option_text' => 'CBNAAT', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q7->id, 'option_text' => 'Chest X-ray', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q7->id, 'option_text' => 'Sputum microscopy', 'is_correct' => false, 'order_by' => 4]);

            $q8 = Question::create([
                'chapter_id' => $drDetectionChapter->id,
                'question_text' => 'Which test detects isoniazid and rifampicin resistance mutations?',
                'difficulty_level' => 'medium',
                'type' => 'single_choice',
                'explanation' => 'Line Probe Assay (LPA) is a rapid molecular method that can detect mutations conferring resistance to both isoniazid and rifampicin.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q8->id, 'option_text' => 'Line Probe Assay (LPA)', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q8->id, 'option_text' => 'CBNAAT', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q8->id, 'option_text' => 'Culture', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q8->id, 'option_text' => 'Sputum microscopy', 'is_correct' => false, 'order_by' => 4]);
        }

        // MDR-TB Treatment
        if ($mdrTreatmentChapter) {
            $q9 = Question::create([
                'chapter_id' => $mdrTreatmentChapter->id,
                'question_text' => 'Which drugs should always be included in Group A of MDR-TB regimen?',
                'difficulty_level' => 'hard',
                'type' => 'single_choice',
                'explanation' => 'Group A includes: Levofloxacin/Moxifloxacin, Bedaquiline, and Linezolid (Mnemonic: LLB). These are the most potent drugs and should always be included if tolerated.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q9->id, 'option_text' => 'Levofloxacin, Bedaquiline, and Linezolid', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q9->id, 'option_text' => 'Rifampicin, Isoniazid, and Pyrazinamide', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q9->id, 'option_text' => 'Ethambutol, Streptomycin, and Thiacetazone', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q9->id, 'option_text' => 'Para-aminosalicylic acid, Ethionamide, and Clofazimine', 'is_correct' => false, 'order_by' => 4]);

            $q10 = Question::create([
                'chapter_id' => $mdrTreatmentChapter->id,
                'question_text' => 'What is the standard duration of MDR-TB treatment according to WHO guidelines?',
                'difficulty_level' => 'hard',
                'type' => 'single_choice',
                'explanation' => 'MDR-TB treatment typically lasts 20 months: 6 months of intensive phase followed by 14 months of continuation phase.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q10->id, 'option_text' => '20 months', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q10->id, 'option_text' => '6 months', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q10->id, 'option_text' => '12 months', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q10->id, 'option_text' => '24 months', 'is_correct' => false, 'order_by' => 4]);
        }

        // New Anti-TB Agents
        if ($newAgentsChapter) {
            $q11 = Question::create([
                'chapter_id' => $newAgentsChapter->id,
                'question_text' => 'Which newer anti-TB drug inhibits mycobacterial ATP synthase?',
                'difficulty_level' => 'hard',
                'type' => 'single_choice',
                'explanation' => 'Bedaquiline is a diarylquinoline that inhibits mycobacterial ATP synthase, leading to rapid bacterial death. It is used in MDR-TB treatment.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q11->id, 'option_text' => 'Bedaquiline', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q11->id, 'option_text' => 'Linezolid', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q11->id, 'option_text' => 'Moxifloxacin', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q11->id, 'option_text' => 'Para-aminosalicylic acid', 'is_correct' => false, 'order_by' => 4]);

            $q12 = Question::create([
                'chapter_id' => $newAgentsChapter->id,
                'question_text' => 'What is the mechanism of action of linezolid in TB treatment?',
                'difficulty_level' => 'hard',
                'type' => 'single_choice',
                'explanation' => 'Linezolid is an oxazolidinone that inhibits bacterial protein synthesis by binding to the 50S ribosomal subunit.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q12->id, 'option_text' => 'Inhibition of bacterial protein synthesis', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q12->id, 'option_text' => 'Inhibition of ATP synthase', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q12->id, 'option_text' => 'Inhibition of DNA gyrase', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q12->id, 'option_text' => 'Inhibition of mycolic acid synthesis', 'is_correct' => false, 'order_by' => 4]);
        }

        // TB Classification
        if ($classificationChapter) {
            $q13 = Question::create([
                'chapter_id' => $classificationChapter->id,
                'question_text' => 'What is "new TB" according to WHO TB case definitions?',
                'difficulty_level' => 'medium',
                'type' => 'single_choice',
                'explanation' => 'New TB refers to patients who have never been treated for TB or who have taken TB drugs for less than 4 weeks.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q13->id, 'option_text' => 'Patients who have never been treated or took <4 weeks of treatment', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q13->id, 'option_text' => 'Patients who relapsed after completing treatment', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q13->id, 'option_text' => 'Patients who failed TB treatment', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q13->id, 'option_text' => 'Patients with treatment interruption', 'is_correct' => false, 'order_by' => 4]);

            $q14 = Question::create([
                'chapter_id' => $classificationChapter->id,
                'question_text' => 'What distinguishes "relapse" TB from "recurrent" TB?',
                'difficulty_level' => 'hard',
                'type' => 'single_choice',
                'explanation' => 'Relapse occurs when TB recurs after successful treatment completion due to incomplete cure or reactivation. Recurrent TB can be relapse or reinfection.',
                'is_published' => true,
            ]);

            Option::create(['question_id' => $q14->id, 'option_text' => 'Relapse occurs within 12 months after treatment completion', 'is_correct' => true, 'order_by' => 1]);
            Option::create(['question_id' => $q14->id, 'option_text' => 'Relapse occurs in patients who never completed treatment', 'is_correct' => false, 'order_by' => 2]);
            Option::create(['question_id' => $q14->id, 'option_text' => 'Relapse is due to drug resistance', 'is_correct' => false, 'order_by' => 3]);
            Option::create(['question_id' => $q14->id, 'option_text' => 'Relapse only occurs in untreated patients', 'is_correct' => false, 'order_by' => 4]);
        }
    }
}
