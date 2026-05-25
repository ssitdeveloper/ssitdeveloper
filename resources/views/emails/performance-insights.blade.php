@component('mail::message')
# Your Weekly Performance Insights

Hi {{ $studentName }},

Here's a summary of your learning progress this week on NEET LMS.

## Weekly Statistics

@component('mail::panel')
| Metric | Value |
|--------|-------|
| **Tests Taken** | {{ $weekStats['tests_taken'] }} |
| **Average Score** | {{ $weekStats['avg_score'] }}% |
| **Study Streak** | {{ $studyStreak }} day{{ $studyStreak != 1 ? 's' : '' }} |
@endcomponent

## Areas to Focus On

The following topics need your attention this week:

@foreach($weakTopics as $topic)
- **{{ $topic['subject']->name }}** - {{ round(($topic['correct'] / max(1, $topic['total'])) * 100) }}% accuracy
@endforeach

## Your Progress

@if($weekStats['tests_taken'] >= 5)
📈 **Excellent Commitment!** You've taken {{ $weekStats['tests_taken'] }} tests this week. Your dedication will pay off!
@elseif($weekStats['tests_taken'] >= 3)
👍 **Good Effort!** Keep practicing regularly for consistent improvement.
@else
💪 **Time to Focus!** Try to take at least 3-5 tests per week for optimal learning.
@endif

@if($studyStreak >= 7)
🔥 **Amazing Streak!** You have a {{ $studyStreak }}-day study streak. Don't break it!
@elseif($studyStreak >= 3)
✨ **Building Momentum!** Your {{ $studyStreak }}-day streak shows dedication. Keep it up!
@endif

## Recommended Actions

1. **Practice Weak Topics** - Focus on {{ $weakTopics[0]['subject']->name ?? 'identified weak areas' }} to boost your score
2. **Review Fundamentals** - Revisit concepts you're struggling with
3. **Take More Tests** - Regular practice is key to improvement

@component('mail::button', ['url' => $dashboardUrl])
View Full Dashboard
@endcomponent

---

## Quick Links

- [View Insights & Recommendations]({{ $insightsUrl }})
- [Access Your Tests]({{ config('app.url') }}/student/my-tests)
- [Continue Practicing]({{ config('app.url') }}/question-bank)

---

Keep up the great work! Consistent effort leads to success.

Best regards,
**NEET LMS Team**

@slot('footer')
You're receiving this email because you're an active NEET LMS student. Have feedback? Contact us anytime!
@endslot
@endcomponent
