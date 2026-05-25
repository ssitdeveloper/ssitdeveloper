@extends('layouts.student')

@section('title', 'Study Materials')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">📚 Study Materials</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Click on any question to reveal the answer</p>
    </div>

    <div style="display: grid; gap: var(--spacing-4);">
        <!-- Module 1: Tuberculosis Diagnosis -->
        <div class="student-card">
            <h2 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-primary); font-size: 20px; border-bottom: 2px solid var(--color-primary); padding-bottom: var(--spacing-2);">
                🔬 Tuberculosis Diagnosis
            </h2>

            <!-- Q1 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q1</span>
                    Which tests are included under NAAT?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">CBNAAT and TruNAAT.</p>
                </div>
            </div>

            <!-- Q2 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q2</span>
                    What does CBNAAT stand for?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">Cartridge-Based Nucleic Acid Amplification Test.</p>
                </div>
            </div>

            <!-- Q3 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q3</span>
                    Which test rapidly detects rifampicin resistance?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">CBNAAT.</p>
                </div>
            </div>

            <!-- Q4 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q4</span>
                    Which investigation is the gold standard for DR-TB diagnosis?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">Culture and drug susceptibility testing (DST).</p>
                </div>
            </div>

            <!-- Q5 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q5</span>
                    Which test detects isoniazid and rifampicin resistance mutations?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">Line Probe Assay (LPA).</p>
                </div>
            </div>
        </div>

        <!-- Module 2: MDR-TB Treatment -->
        <div class="student-card">
            <h2 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-primary); font-size: 20px; border-bottom: 2px solid var(--color-primary); padding-bottom: var(--spacing-2);">
                💊 MDR-TB Treatment
            </h2>

            <!-- Q6 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q6</span>
                    Which drugs should always be included in Group A of MDR-TB regimen?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">Levofloxacin/moxifloxacin, bedaquiline, and linezolid.</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e; font-style: italic;">💡 Mnemonic: <strong>LLB</strong> (Levo, Linezolid, Bedaquiline)</p>
                </div>
            </div>

            <!-- Q7 -->
            <div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
                    <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q7</span>
                    Which newer anti-TB drug inhibits mycobacterial ATP synthase?
                </p>
                <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
                    Click for Answer
                </button>
                <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
                    <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
                    <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">Bedaquiline.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.student-card {
    background-color: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-4);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.qa-toggle-btn:hover {
    background-color: #0d47a1 !important;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.qa-toggle-btn.active {
    background-color: #4caf50 !important;
}

.qa-toggle-btn svg {
    transition: transform 0.3s ease;
}

.qa-toggle-btn.active svg {
    transform: rotate(180deg);
}
</style>

<script>
function toggleQA(btn) {
    const item = btn.closest('.qa-item');
    const answer = item.querySelector('.qa-answer');
    const icon = btn.querySelector('svg');

    if (answer.style.display === 'none') {
        // Show answer
        answer.style.display = 'block';
        btn.textContent = '';
        btn.innerHTML = '<svg style="width: 16px; height: 16px;" data-lucide="chevron-up"></svg> Hide Answer';
        btn.classList.add('active');
        btn.style.backgroundColor = '#4caf50';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } else {
        // Hide answer
        answer.style.display = 'none';
        btn.textContent = '';
        btn.innerHTML = '<svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg> Click for Answer';
        btn.classList.remove('active');
        btn.style.backgroundColor = 'var(--color-primary)';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection
