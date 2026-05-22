@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.questions.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Questions</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Edit Question</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <strong>Error:</strong>
                <ul style="margin: var(--spacing-2) 0 0 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.questions.update', $question) }}" style="display: grid; gap: var(--spacing-4);">
            @csrf
            @method('PUT')

            <!-- Chapter -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Chapter <span style="color: var(--color-danger);">*</span></label>
                <select name="chapter_id" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;">
                    <option value="">Select a chapter</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}" @if(old('chapter_id', $question->chapter_id) === (string)$chapter->id) selected @endif>
                            {{ $chapter->topic->subject->name }} - {{ $chapter->topic->name }} - {{ $chapter->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Question Text -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Question Text <span style="color: var(--color-danger);">*</span></label>
                <textarea name="question_text" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; min-height: 120px;">{{ old('question_text', $question->question_text) }}</textarea>
            </div>

            <!-- Difficulty Level -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Difficulty Level <span style="color: var(--color-danger);">*</span></label>
                <select name="difficulty_level" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;">
                    <option value="">Select difficulty</option>
                    @foreach($difficulties as $difficulty)
                        <option value="{{ $difficulty->value }}" @if(old('difficulty_level', $question->difficulty_level->value) === $difficulty->value) selected @endif>{{ ucfirst($difficulty->value) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Explanation -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Explanation (Optional)</label>
                <textarea name="explanation" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; min-height: 80px; resize: vertical;">{{ old('explanation', $question->explanation) }}</textarea>
                <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Provide a detailed explanation for the correct answer</p>
            </div>

            <!-- Publish Status -->
            <div style="display: flex; align-items: center; gap: var(--spacing-2); padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg); border: 1px solid var(--color-gray-300);">
                <input type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $question->is_published)) style="width: 20px; height: 20px; cursor: pointer;">
                <label for="is_published" style="margin: 0; font-weight: var(--font-weight-medium); color: var(--color-gray-900); cursor: pointer; flex: 1;">
                    Publish this question
                </label>
                <span style="color: var(--color-gray-600); font-size: var(--font-size-sm);">Published questions are visible to students</span>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: var(--spacing-2); margin-top: var(--spacing-4);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; transition: all var(--transition-fast);">
                    Update Question
                </button>
                <a href="{{ route('admin.questions.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; text-decoration: none; display: inline-block; transition: all var(--transition-fast);">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
