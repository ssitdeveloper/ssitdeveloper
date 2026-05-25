<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Tuberculosis',
                'description' => 'Comprehensive coverage of TB diagnosis, classification, treatment, and complications',
                'icon' => '🫁',
                'color' => '#EF4444',
                'order_by' => 1,
            ],
            [
                'name' => 'Respiratory Diseases',
                'description' => 'Asthma, COPD, Pneumonia, and other respiratory conditions',
                'icon' => '🌬️',
                'color' => '#3B82F6',
                'order_by' => 2,
            ],
            [
                'name' => 'Infectious Diseases',
                'description' => 'Viral, bacterial, parasitic, and fungal infections',
                'icon' => '🦠',
                'color' => '#8B5CF6',
                'order_by' => 3,
            ],
            [
                'name' => 'Cardiovascular Diseases',
                'description' => 'Heart conditions, hypertension, and vascular diseases',
                'icon' => '❤️',
                'color' => '#DC2626',
                'order_by' => 4,
            ],
            [
                'name' => 'Gastrointestinal Diseases',
                'description' => 'Digestive system disorders and GI pathology',
                'icon' => '🍽️',
                'color' => '#F59E0B',
                'order_by' => 5,
            ],
            [
                'name' => 'Neurology',
                'description' => 'Neurological disorders and diseases of nervous system',
                'icon' => '🧠',
                'color' => '#6366F1',
                'order_by' => 6,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
