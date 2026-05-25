<!-- Q&A Item Component -->
<!-- Usage: @include('student.partials.qa-item', ['number' => 1, 'question' => 'Question text?', 'answer' => 'Answer text', 'note' => 'Optional note']) -->

<div class="qa-item" style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); margin-bottom: var(--spacing-2); background: var(--color-gray-50);">
    <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: var(--color-gray-900); font-size: 16px;">
        <span style="background: #e0e7ff; padding: 4px 8px; border-radius: 4px; color: #4f46e5;">Q{{ $number ?? 1 }}</span>
        {{ $question ?? 'Question text' }}
    </p>
    <button class="qa-toggle-btn" onclick="toggleQA(this)" style="margin: var(--spacing-2) 0 0 0; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px;" data-lucide="chevron-down"></svg>
        Click for Answer
    </button>
    <div class="qa-answer" style="display: none; margin-top: var(--spacing-2); padding: var(--spacing-2); background: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg);">
        <p style="margin: 0; color: #0c4a6e; font-weight: var(--font-weight-medium);">✓ Answer:</p>
        <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e;">{{ $answer ?? 'Answer text' }}</p>
        @if($note ?? null)
            <p style="margin: var(--spacing-1) 0 0 0; color: #0c4a6e; font-style: italic;">💡 {{ $note }}</p>
        @endif
    </div>
</div>
