@extends('layouts.app')

@section('title', 'Question Bank')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Question Bank</h1>
                    <p class="mt-2 text-gray-600">Practice questions across all subjects and topics</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('question-bank.practice') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Practice Mode
                    </a>
                    <a href="{{ route('question-bank.bookmarks') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-bookmark mr-2"></i>My Bookmarks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>

                    <!-- Search -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Questions</label>
                        <form method="GET" action="{{ route('question-bank.search') }}" class="flex">
                            <input type="text" name="query" placeholder="Search questions..."
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Subject Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select id="subject-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Topic Filter -->
                    <div class="mb-6" id="topic-filter" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Topic</label>
                        <select id="topic-select" name="topic_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Topics</option>
                        </select>
                    </div>

                    <!-- Chapter Filter -->
                    <div class="mb-6" id="chapter-filter" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chapter</label>
                        <select id="chapter-select" name="chapter_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Chapters</option>
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                        <select name="difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Levels</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>

                    <button type="button" id="apply-filters" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Questions List -->
            <div class="lg:col-span-3">
                <div class="space-y-6">
                    @forelse($questions as $question)
                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($question->difficulty_level === 'easy') bg-green-100 text-green-800
                                            @elseif($question->difficulty_level === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($question->difficulty_level->value) }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $question->chapter->topic->subject->name }} >
                                            {{ $question->chapter->topic->name }} >
                                            {{ $question->chapter->name }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-medium text-gray-900 mb-3">
                                        {!! Str::limit(strip_tags($question->question_text), 150) !!}
                                    </h3>

                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span><i class="fas fa-eye mr-1"></i>{{ $question->views_count }} views</span>
                                        <span><i class="fas fa-check-circle mr-1"></i>{{ $question->attempts_count }} attempts</span>
                                    </div>
                                </div>

                                <div class="ml-4 flex flex-col space-y-2">
                                    <a href="{{ route('question-bank.show', $question->id) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        Practice
                                    </a>
                                    <form method="POST" action="{{ route('question-bank.toggle-bookmark', $question->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-700 text-sm">
                                            <i class="fas fa-bookmark"></i> Bookmark
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No questions found</h3>
                            <p class="text-gray-600">Try adjusting your filters or search terms.</p>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($questions->hasPages())
                        <div class="bg-white px-4 py-3 rounded-lg shadow-sm">
                            {{ $questions->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subjectSelect = document.getElementById('subject-select');
    const topicSelect = document.getElementById('topic-select');
    const chapterSelect = document.getElementById('chapter-select');
    const topicFilter = document.getElementById('topic-filter');
    const chapterFilter = document.getElementById('chapter-filter');
    const applyFilters = document.getElementById('apply-filters');

    // Load topics when subject changes
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        if (subjectId) {
            fetch(`/question-bank/api/topics/${subjectId}`)
                .then(response => response.json())
                .then(data => {
                    topicSelect.innerHTML = '<option value="">All Topics</option>';
                    data.topics.forEach(topic => {
                        topicSelect.innerHTML += `<option value="${topic.id}">${topic.name}</option>`;
                    });
                    topicFilter.style.display = 'block';
                    chapterFilter.style.display = 'none';
                });
        } else {
            topicFilter.style.display = 'none';
            chapterFilter.style.display = 'none';
        }
    });

    // Load chapters when topic changes
    topicSelect.addEventListener('change', function() {
        const topicId = this.value;
        if (topicId) {
            fetch(`/question-bank/api/chapters/${topicId}`)
                .then(response => response.json())
                .then(data => {
                    chapterSelect.innerHTML = '<option value="">All Chapters</option>';
                    data.chapters.forEach(chapter => {
                        chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
                    });
                    chapterFilter.style.display = 'block';
                });
        } else {
            chapterFilter.style.display = 'none';
        }
    });

    // Apply filters
    applyFilters.addEventListener('click', function() {
        const params = new URLSearchParams(window.location.search);

        // Update or remove subject
        const subjectId = subjectSelect.value;
        if (subjectId) {
            params.set('subject_id', subjectId);
        } else {
            params.delete('subject_id');
            params.delete('topic_id');
            params.delete('chapter_id');
        }

        // Update or remove topic
        const topicId = topicSelect.value;
        if (topicId) {
            params.set('topic_id', topicId);
        } else {
            params.delete('topic_id');
            params.delete('chapter_id');
        }

        // Update or remove chapter
        const chapterId = chapterSelect.value;
        if (chapterId) {
            params.set('chapter_id', chapterId);
        } else {
            params.delete('chapter_id');
        }

        // Update difficulty
        const difficulty = document.querySelector('select[name="difficulty"]').value;
        if (difficulty) {
            params.set('difficulty', difficulty);
        } else {
            params.delete('difficulty');
        }

        // Redirect with new filters
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    });

    // Initialize filters on page load
    if (subjectSelect.value) {
        subjectSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection