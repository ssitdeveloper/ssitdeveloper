@component('mail::message')
# Recommended Topics to Practice

Hi {{ $studentName }},

Based on your recent test performance, we've identified **{{ $topicsCount }} topic{{ $topicsCount != 1 ? 's' : '' }}** where you can improve.

## Your Weak Areas

@foreach($weakTopics as $index => $topic)
### {{ $index + 1 }}. {{ $topic['subject']->name }}
- **Current Accuracy:** {{ $topic['accuracy'] }}%
- **Difficulty Level:** {{ $topic['difficulty'] }}
- **Action:** {{ $topic['accuracy'] < 40 ? 'Review basics and take easy practice questions' : 'Practice more questions to strengthen' }}

@endforeach

## How to Improve

### Step 1: Understand the Concept
Review the fundamental concepts and formulas related to these topics.

### Step 2: Practice Easy Questions
Start with basic-level questions to build confidence.

### Step 3: Attempt Medium Questions
Once comfortable, move to medium-difficulty questions.

### Step 4: Challenge Yourself
Finally, attempt hard questions to master the topic.

### Step 5: Track Progress
Monitor your improvement with regular practice tests.

@component('mail::button', ['url' => $practiceUrl])
Start Practicing Now
@endcomponent

## Quick Stats

- **Total Weak Topics:** {{ $topicsCount }}
- **Recommended Practice:** 2-3 hours per weak topic
- **Expected Improvement:** 10-15% in 1 week with consistent practice

## Your Learning Path

1. Focus on ONE topic at a time
2. Practice at least 20-30 questions per topic
3. Review explanations for incorrect answers
4. Take a test to check your improvement
5. Move to the next topic

---

## Success Tips

✅ **Be Consistent** - Practice daily, even if just for 30 minutes
✅ **Understand Concepts** - Don't just memorize; understand why
✅ **Track Progress** - Use analytics to monitor improvement
✅ **Ask for Help** - Don't hesitate to seek clarification
✅ **Stay Motivated** - Every small improvement counts!

---

Remember, every expert was once a beginner. Your effort today will reflect in your results tomorrow!

@component('mail::button', ['url' => $dashboardUrl])
View Your Dashboard
@endcomponent

---

Best regards,
**NEET LMS Team**

@slot('footer')
This email is based on your actual performance. Review your analytics anytime in your dashboard!
@endslot
@endcomponent
