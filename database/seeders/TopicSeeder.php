<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\Subject;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Tuberculosis subject
        $tbSubject = Subject::where('name', 'Tuberculosis')->first();
        $respSubject = Subject::where('name', 'Respiratory Diseases')->first();
        $infectSubject = Subject::where('name', 'Infectious Diseases')->first();
        $cardioSubject = Subject::where('name', 'Cardiovascular Diseases')->first();

        // TB Topics
        if ($tbSubject) {
            $tbTopics = [
                [
                    'name' => 'TB Epidemiology & Pathogenesis',
                    'description' => 'History, epidemiology, transmission, and pathophysiology of TB',
                    'order_by' => 1,
                ],
                [
                    'name' => 'TB Diagnosis',
                    'description' => 'Diagnostic methods, imaging, and molecular tests for TB',
                    'order_by' => 2,
                ],
                [
                    'name' => 'TB Classification & Complications',
                    'description' => 'Types of TB and major complications',
                    'order_by' => 3,
                ],
                [
                    'name' => 'TB Treatment & Management',
                    'description' => 'First-line and second-line drugs, regimens, and monitoring',
                    'order_by' => 4,
                ],
                [
                    'name' => 'Drug-Resistant TB',
                    'description' => 'MDR-TB, XDR-TB, and newer agents',
                    'order_by' => 5,
                ],
            ];

            foreach ($tbTopics as $topic) {
                Topic::create([
                    'subject_id' => $tbSubject->id,
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'order_by' => $topic['order_by'],
                ]);
            }
        }

        // Respiratory Disease Topics
        if ($respSubject) {
            $respTopics = [
                ['name' => 'Asthma', 'description' => 'Classification, pathophysiology, and management', 'order_by' => 1],
                ['name' => 'COPD', 'description' => 'Emphysema, chronic bronchitis, and treatment', 'order_by' => 2],
                ['name' => 'Pneumonia', 'description' => 'Community-acquired and hospital-acquired pneumonia', 'order_by' => 3],
                ['name' => 'Interstitial Lung Diseases', 'description' => 'ILD, pulmonary fibrosis, and related conditions', 'order_by' => 4],
            ];

            foreach ($respTopics as $topic) {
                Topic::create([
                    'subject_id' => $respSubject->id,
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'order_by' => $topic['order_by'],
                ]);
            }
        }

        // Infectious Disease Topics
        if ($infectSubject) {
            $infectTopics = [
                ['name' => 'Bacterial Infections', 'description' => 'Gram-positive and gram-negative bacterial diseases', 'order_by' => 1],
                ['name' => 'Viral Infections', 'description' => 'Viral diseases and their management', 'order_by' => 2],
                ['name' => 'Parasitic Infections', 'description' => 'Helminthic and protozoan infections', 'order_by' => 3],
                ['name' => 'Fungal Infections', 'description' => 'Mycotic infections and treatment', 'order_by' => 4],
            ];

            foreach ($infectTopics as $topic) {
                Topic::create([
                    'subject_id' => $infectSubject->id,
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'order_by' => $topic['order_by'],
                ]);
            }
        }

        // Cardiovascular Topics
        if ($cardioSubject) {
            $cardioTopics = [
                ['name' => 'Hypertension', 'description' => 'Classification, pathophysiology, and management', 'order_by' => 1],
                ['name' => 'Ischemic Heart Disease', 'description' => 'Angina and myocardial infarction', 'order_by' => 2],
                ['name' => 'Heart Failure', 'description' => 'Systolic and diastolic heart failure', 'order_by' => 3],
                ['name' => 'Arrhythmias', 'description' => 'Atrial and ventricular arrhythmias', 'order_by' => 4],
            ];

            foreach ($cardioTopics as $topic) {
                Topic::create([
                    'subject_id' => $cardioSubject->id,
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'order_by' => $topic['order_by'],
                ]);
            }
        }
    }
}
