<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\Topic;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TB Diagnosis Chapters
        $tbDiagnosisTopic = Topic::where('name', 'TB Diagnosis')->first();
        if ($tbDiagnosisTopic) {
            $chapters = [
                ['name' => 'Clinical Presentation of TB', 'description' => 'Symptoms and signs of tuberculosis'],
                ['name' => 'Sputum Microscopy (AFB)', 'description' => 'Acid-fast bacilli staining and examination'],
                ['name' => 'CBNAAT & TruNAAT', 'description' => 'Rapid nucleic acid amplification tests'],
                ['name' => 'TB Culture & DST', 'description' => 'Culture methods and drug susceptibility testing'],
                ['name' => 'Radiological Findings', 'description' => 'Chest X-ray patterns in TB'],
                ['name' => 'Tuberculin Skin Test', 'description' => 'Mantoux test and interpretation'],
                ['name' => 'IGRA & Serology', 'description' => 'Interferon gamma release assays'],
            ];

            foreach ($chapters as $index => $chapter) {
                Chapter::create([
                    'topic_id' => $tbDiagnosisTopic->id,
                    'name' => $chapter['name'],
                    'description' => $chapter['description'],
                    'order_by' => $index + 1,
                ]);
            }
        }

        // TB Classification & Complications
        $tbClassTopic = Topic::where('name', 'TB Classification & Complications')->first();
        if ($tbClassTopic) {
            $chapters = [
                ['name' => 'Classification of TB', 'description' => 'New, recurrent, relapse, treatment failure'],
                ['name' => 'Extra-pulmonary TB', 'description' => 'TB affecting organs other than lungs'],
                ['name' => 'Miliary TB', 'description' => 'Disseminated tuberculosis'],
                ['name' => 'TB Meningitis', 'description' => 'Tuberculous meningitis - diagnosis and treatment'],
                ['name' => 'Hemoptysis in TB', 'description' => 'Causes and management of bleeding'],
                ['name' => 'Tuberculomas', 'description' => 'TB-related masses and lesions'],
            ];

            foreach ($chapters as $index => $chapter) {
                Chapter::create([
                    'topic_id' => $tbClassTopic->id,
                    'name' => $chapter['name'],
                    'description' => $chapter['description'],
                    'order_by' => $index + 1,
                ]);
            }
        }

        // TB Treatment & Management
        $tbTreatmentTopic = Topic::where('name', 'TB Treatment & Management')->first();
        if ($tbTreatmentTopic) {
            $chapters = [
                ['name' => 'Anti-TB Drugs', 'description' => 'Mechanisms and side effects of TB medications'],
                ['name' => 'Standard TB Regimens', 'description' => '2-month intensive + 4-month continuation phase'],
                ['name' => 'Drug Interactions', 'description' => 'TB drug interactions with other medications'],
                ['name' => 'Adverse Drug Reactions', 'description' => 'Management of ADRs in TB treatment'],
                ['name' => 'TB-DOTS Strategy', 'description' => 'Directly observed therapy short course'],
                ['name' => 'Monitoring & Follow-up', 'description' => 'Clinical and radiological monitoring'],
            ];

            foreach ($chapters as $index => $chapter) {
                Chapter::create([
                    'topic_id' => $tbTreatmentTopic->id,
                    'name' => $chapter['name'],
                    'description' => $chapter['description'],
                    'order_by' => $index + 1,
                ]);
            }
        }

        // Drug-Resistant TB
        $drTbTopic = Topic::where('name', 'Drug-Resistant TB')->first();
        if ($drTbTopic) {
            $chapters = [
                ['name' => 'MDR-TB Classification', 'description' => 'Rifampicin and isoniazid resistance'],
                ['name' => 'XDR-TB', 'description' => 'Extensively drug-resistant tuberculosis'],
                ['name' => 'Rapid Drug Resistance Detection', 'description' => 'Line Probe Assay and molecular methods'],
                ['name' => 'New Anti-TB Agents', 'description' => 'Bedaquiline, linezolid, moxifloxacin'],
                ['name' => 'MDR-TB Treatment Regimens', 'description' => 'Group A, B, C drugs classification'],
                ['name' => 'Management of Treatment Failure', 'description' => 'Strategies for resistant cases'],
            ];

            foreach ($chapters as $index => $chapter) {
                Chapter::create([
                    'topic_id' => $drTbTopic->id,
                    'name' => $chapter['name'],
                    'description' => $chapter['description'],
                    'order_by' => $index + 1,
                ]);
            }
        }

        // Respiratory Diseases - Asthma
        $asthmaTopic = Topic::where('name', 'Asthma')->first();
        if ($asthmaTopic) {
            $chapters = [
                ['name' => 'Asthma Definition & Pathophysiology', 'description' => 'Understanding asthma mechanisms'],
                ['name' => 'Classification of Asthma', 'description' => 'Intermittent, mild-persistent, moderate-persistent, severe-persistent'],
                ['name' => 'Clinical Features', 'description' => 'Symptoms and signs of asthma'],
                ['name' => 'Asthma Diagnosis', 'description' => 'Spirometry and PFTs'],
                ['name' => 'Asthma Management', 'description' => 'Controller and reliever medications'],
            ];

            foreach ($chapters as $index => $chapter) {
                Chapter::create([
                    'topic_id' => $asthmaTopic->id,
                    'name' => $chapter['name'],
                    'description' => $chapter['description'],
                    'order_by' => $index + 1,
                ]);
            }
        }
    }
}
