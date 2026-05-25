<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Attempt - NEET LMS</title>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .test-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-meta {
            flex: 1;
        }
        .test-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .test-subtitle {
            font-size: 14px;
            color: #64748b;
        }
        .timer-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fef3c7;
            padding: 12px 16px;
            border-radius: 8px;
            font-weight: 600;
            color: #92400e;
        }
        .timer-icon {
            width: 20px;
            height: 20px;
        }
        .questions-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .question {
            padding: 24px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #f8fafc;
            transition: all 0.3s ease;
        }
        .question:hover {
            border-color: #cbd5e1;
        }
        .question.answered {
            background: #f0f9ff;
            border-color: #0284c7;
        }
        .question-number {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .question-text {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 18px;
            color: #0f172a;
            line-height: 1.6;
        }
        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .option {
            display: flex;
            align-items: flex-start;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        .option:hover {
            background-color: #f1f5f9;
            border-color: #0284c7;
        }
        .option input[type="radio"] {
            margin-right: 14px;
            margin-top: 3px;
            cursor: pointer;
            width: 20px;
            height: 20px;
            accent-color: #0284c7;
        }
        .option-text {
            flex: 1;
            font-size: 15px;
            color: #1e293b;
            line-height: 1.5;
        }
        .option.selected {
            background: #f0f9ff;
            border-color: #0284c7;
        }
        .option.selected input[type="radio"] {
            accent-color: #0284c7;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            padding-top: 28px;
            border-top: 2px solid #e2e8f0;
        }
        .submit-btn, .end-test-btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
        }
        .submit-btn {
            background: #10b981;
            color: white;
        }
        .submit-btn:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .end-test-btn {
            background: #f1f5f9;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }
        .end-test-btn:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }
        .btn-icon {
            width: 18px;
            height: 18px;
        }
        .no-questions {
            text-align: center;
            padding: 48px 24px;
            color: #64748b;
        }
        .question-count {
            color: #64748b;
            font-size: 14px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="test-info">
                <div class="test-meta">
                    <h1 class="test-title">{{ $test->title }}</h1>
                    <p class="test-subtitle">Attempt #{{ $attempt->id }} • Duration: {{ $test->duration_minutes }} minutes</p>
                </div>
                <div class="timer-badge">
                    <svg class="timer-icon" data-lucide="clock"></svg>
                    {{ $test->duration_minutes }} mins
                </div>
            </div>
        </div>

        <div class="questions-container">
            @php
                $questionCount = count($questions);
                $questionIds = json_decode($attempt->question_ids ?? '[]', true) ?? [];
                $questionIdsCount = count($questionIds);
            @endphp

            @if($questionCount > 0)
                <form method="POST" action="{{ route('student.tests.submit-answer', ['slug' => $test->slug]) }}" id="testForm">
                    @csrf
                    <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

                    @foreach($questions as $index => $question)
                        <div class="question" id="question-{{ $question->id }}">
                            <div class="question-number">Question {{ $index + 1 }} of {{ $questionCount }}</div>
                            <div class="question-text">
                                {{ $question->question_text ?? 'Question ' . ($index + 1) }}
                            </div>

                            @php
                                $options = collect();
                                if ($question->relationLoaded('options')) {
                                    $options = $question->options ?? collect();
                                } else {
                                    $options = \App\Models\Option::where('question_id', $question->id)->orderBy('order_by')->get();
                                }
                                $optionCount = $options->count();
                            @endphp

                            @if($optionCount > 0)
                                <div class="options">
                                    @foreach($options as $option)
                                        <label class="option">
                                            <input type="radio"
                                                   name="answers[{{ $question->id }}]"
                                                   value="{{ $option->id }}"
                                                   onchange="document.getElementById('question-{{ $question->id }}').classList.add('answered')">
                                            <span class="option-text">{{ $option->option_text ?? 'Option ' . ($loop->iteration) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: #ef4444; padding: 10px; background: #fee2e2; border-radius: 6px; margin-top: 10px;">
                                    ⚠️ This question has no answer options. Please contact your administrator.
                                </p>
                            @endif
                        </div>
                    @endforeach

                    <div class="action-buttons">
                        <button type="submit" class="submit-btn">
                            <svg class="btn-icon" data-lucide="check"></svg>
                            Submit Test
                        </button>
                        <button type="button" class="end-test-btn" onclick="if(confirm('Are you sure you want to end the test? Your current answers will be submitted.')) document.getElementById('testForm').submit();">
                            <svg class="btn-icon" data-lucide="x"></svg>
                            End Test
                        </button>
                    </div>
                </form>
            @else
                <div class="no-questions" style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 8px;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: #94a3b8;" data-lucide="inbox"></svg>
                    <p style="margin-bottom: 8px; font-weight: 600; font-size: 18px;">No Questions Found</p>
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 10px;">Unable to load questions for this test.</p>
                    @if($questionIdsCount === 0)
                        <p style="font-size: 12px; color: #e11d48; background: #ffe4e6; padding: 8px; border-radius: 4px; display: inline-block;">
                            ⚠️ No questions were assigned to this test. Contact your administrator.
                        </p>
                    @else
                        <p style="font-size: 12px; color: #e11d48; background: #ffe4e6; padding: 8px; border-radius: 4px; display: inline-block;">
                            ⚠️ Found {{ $questionIdsCount }} question(s) in attempt but couldn't load them. Contact support.
                        </p>
                    @endif
                </div>
            @endif

            <div class="question-count" style="text-align: center; padding: 20px; color: #64748b; font-size: 12px;">
                <strong>{{ $questionCount }}</strong> question(s) loaded • <strong>{{ $questionIdsCount }}</strong> question ID(s) in attempt
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
