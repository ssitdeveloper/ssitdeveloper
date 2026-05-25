@component('mail::message')
# Test Result Summary

Hi {{ $studentName }},

Congratulations! You've completed the test **{{ $testName }}**.

## Your Performance

@component('mail::panel')
| Metric | Value |
|--------|-------|
| **Score** | {{ $score }}% |
| **Accuracy** | {{ $accuracy }}% |
| **Questions Attempted** | {{ $questionsAnswered }}/{{ $totalQuestions }} |
| **Questions Skipped** | {{ $questionsSkipped }} |
| **Time Taken** | {{ $duration }} minutes |
@endcomponent

## What's Next?

We've analyzed your answers and identified areas where you can improve. Here are your recommended actions:

@component('mail::button', ['url' => $resultUrl])
View Detailed Results
@endcomponent

### Additional Resources

- [Review Your Answers]({{ $reviewUrl }}) - Go through each question with explanations
- [Get Personalized Recommendations]({{ $recommendationsUrl }}) - Practice questions on weak topics
- [Return to Dashboard]({{ config('app.url') }}/student/dashboard) - Continue your learning journey

## Performance Insights

@if($accuracy >= 90)
🌟 **Outstanding Performance!** You've demonstrated excellent understanding of the material. Keep up the momentum!
@elseif($accuracy >= 75)
✅ **Great Job!** You're on track with solid understanding. Focus on the challenging areas for improvement.
@elseif($accuracy >= 60)
📚 **Good Progress!** There's room for improvement. Review the weak topics and practice more questions.
@else
💪 **Keep Going!** Don't get discouraged. Consistent practice will help you improve. Focus on fundamentals and practice daily.
@endif

---

Thanks for using NEET LMS. We're here to help you achieve your goals!

Best regards,
**NEET LMS Team**

@slot('footer')
© {{ date('Y') }} NEET LMS. All rights reserved.
@endslot
@endcomponent
