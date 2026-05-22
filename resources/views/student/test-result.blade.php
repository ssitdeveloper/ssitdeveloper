<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results - NEET LMS</title>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .result-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .result-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .result-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
        }
        .result-header-content {
            position: relative;
            z-index: 1;
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .header-icon {
            width: 32px;
            height: 32px;
            color: #10b981;
        }
        .test-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .test-subtitle {
            font-size: 14px;
            opacity: 0.85;
            margin-bottom: 32px;
            font-weight: 500;
        }
        .score-container {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 24px;
        }
        .score-item {
            text-align: center;
        }
        .score-label {
            font-size: 12px;
            opacity: 0.75;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .score-value {
            font-size: 56px;
            font-weight: 800;
            line-height: 1;
        }
        .score-divider {
            width: 2px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
        }
        .percentage {
            font-size: 14px;
            opacity: 0.85;
            margin-top: 8px;
        }
        .result-body {
            padding: 50px 40px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }
        .stat-box {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 16px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .stat-icon {
            width: 28px;
            height: 28px;
            margin: 0 auto 10px;
            color: #64748b;
        }
        .stat-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-value.correct {
            color: #10b981;
        }
        .stat-value.wrong {
            color: #ef4444;
        }
        .stat-value.unanswered {
            color: #f59e0b;
        }
        .performance-section {
            margin-bottom: 40px;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e2e8f0;
        }
        .section-icon {
            width: 24px;
            height: 24px;
            color: #0f172a;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .performance-chart {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding: 24px;
            background: #f8fafc;
            border-radius: 12px;
        }
        .chart-item {
            text-align: center;
        }
        .chart-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        .chart-circle.correct {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .chart-circle.wrong {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .chart-circle.unanswered {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .chart-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .progress-section {
            padding: 24px;
            background: #f8fafc;
            border-radius: 12px;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .progress-label-text {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .progress-label-value {
            font-size: 24px;
            font-weight: 700;
            color: #10b981;
        }
        .progress-bar {
            background: #e2e8f0;
            border-radius: 8px;
            height: 12px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.5s ease;
            border-radius: 8px;
        }
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #e2e8f0;
        }
        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
        }
        .btn-icon {
            width: 18px;
            height: 18px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }
        .footer-meta {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }
        .meta-item {
            display: inline-block;
            margin: 0 12px;
        }
        @media (max-width: 640px) {
            .result-header {
                padding: 40px 24px;
            }
            .result-body {
                padding: 32px 24px;
            }
            .score-container {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .score-divider {
                display: none;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .performance-chart {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <!-- Header -->
            <div class="result-header">
                <div class="result-header-content">
                    <div class="header-top">
                        <svg class="header-icon" data-lucide="check-circle"></svg>
                        <span style="font-weight: 600; font-size: 16px;">Test Completed</span>
                    </div>
                    <div class="test-title">{{ $test->title }}</div>
                    <div class="test-subtitle">Attempt #{{ $attempt->id }} • {{ $attempt->started_at?->format('M d, Y') }}</div>

                    <div class="score-container">
                        <div class="score-item">
                            <div class="score-label">Obtained</div>
                            <div class="score-value">{{ round($results['obtained_marks'], 0) }}</div>
                            <div class="percentage">out of {{ round($results['total_marks'], 0) }}</div>
                        </div>
                        <div class="score-divider"></div>
                        <div class="score-item">
                            <div class="score-label">Percentage</div>
                            <div class="score-value">{{ round($results['percentage'], 1) }}%</div>
                            <div class="percentage">accuracy rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="result-body">
                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-box">
                        <svg class="stat-icon" data-lucide="list"></svg>
                        <div class="stat-label">Total Questions</div>
                        <div class="stat-value">{{ $results['total_questions'] }}</div>
                    </div>
                    <div class="stat-box">
                        <svg class="stat-icon" data-lucide="check"></svg>
                        <div class="stat-label">Attempted</div>
                        <div class="stat-value">{{ $results['attempted'] }}</div>
                    </div>
                    <div class="stat-box">
                        <svg class="stat-icon" data-lucide="thumbs-up"></svg>
                        <div class="stat-label">Correct</div>
                        <div class="stat-value correct">{{ $results['correct'] }}</div>
                    </div>
                    <div class="stat-box">
                        <svg class="stat-icon" data-lucide="x"></svg>
                        <div class="stat-label">Wrong</div>
                        <div class="stat-value wrong">{{ $results['wrong'] }}</div>
                    </div>
                    <div class="stat-box">
                        <svg class="stat-icon" data-lucide="help-circle"></svg>
                        <div class="stat-label">Unanswered</div>
                        <div class="stat-value unanswered">{{ $results['unanswered'] }}</div>
                    </div>
                    <div class="stat-box">
                        <svg class="stat-icon" data-lucide="award"></svg>
                        <div class="stat-label">Marks Obtained</div>
                        <div class="stat-value">{{ round($results['obtained_marks'], 1) }}</div>
                    </div>
                </div>

                <!-- Performance Section -->
                <div class="performance-section">
                    <div class="section-header">
                        <svg class="section-icon" data-lucide="bar-chart-3"></svg>
                        <span class="section-title">Performance Overview</span>
                    </div>
                    <div class="performance-chart">
                        <div class="chart-item">
                            <div class="chart-circle correct">{{ $results['correct'] }}</div>
                            <div class="chart-label">Correct</div>
                        </div>
                        <div class="chart-item">
                            <div class="chart-circle wrong">{{ $results['wrong'] }}</div>
                            <div class="chart-label">Wrong</div>
                        </div>
                        <div class="chart-item">
                            <div class="chart-circle unanswered">{{ $results['unanswered'] }}</div>
                            <div class="chart-label">Unanswered</div>
                        </div>
                    </div>
                </div>

                <!-- Accuracy Progress Bar -->
                @php
                    $accuracy = $results['total_questions'] > 0 ? ($results['correct'] / $results['total_questions']) * 100 : 0;
                @endphp
                <div class="performance-section">
                    <div class="section-header">
                        <svg class="section-icon" data-lucide="target"></svg>
                        <span class="section-title">Accuracy Rate</span>
                    </div>
                    <div class="progress-section">
                        <div class="progress-label">
                            <span class="progress-label-text">Your Accuracy</span>
                            <span class="progress-label-value">{{ round($accuracy, 0) }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $accuracy }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        <svg class="btn-icon" data-lucide="arrow-left"></svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('student.tests') }}" class="btn btn-primary">
                        Next Test
                        <svg class="btn-icon" data-lucide="arrow-right"></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-meta">
            <span class="meta-item">Test ID: {{ $attempt->id }}</span>
            <span class="meta-item">Started: {{ $attempt->started_at?->format('M d, Y H:i') }}</span>
            <span class="meta-item">Completed: {{ $attempt->submitted_at?->format('M d, Y H:i') }}</span>
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
