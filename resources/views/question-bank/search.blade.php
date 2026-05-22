@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
                    <p class="mt-2 text-gray-600">
                        @if($query)
                            Results for "{{ $query }}"
                        @else
                            All questions
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('question-bank.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Question Bank
                    </a>
                    <a href="{{ route('question-bank.practice') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Practice Mode
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Search Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Refine Search</h3>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('question-bank.search') }}" class="mb-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Query</label>
                            <input type="text" name="query" value="{{ $query }}"
                                   placeholder="Search questions..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <select name="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Subjects</option>
                                @foreach(\App\Models\Subject::all() as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                            <select name="difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Levels</option>
                                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>

                    @if($query)
                        <div class="border-t pt-4">
                            <div class="text-sm text-gray-600">
                                <strong>{{ $results->total() }}</strong> results found
                                @if($results->total() > 0)
                                    ({{ $results->firstItem() }}-{{ $results->lastItem() }} of {{ $results->total() }})
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Search Results -->
            <div class="lg:col-span-3">
                @if($query && $results->isEmpty())
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                        <p class="text-gray-600 mb-6">Try different keywords or adjust your filters.</p>
                        <a href="{{ route('question-bank.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Browse All Questions
                        </a>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($results as $question)
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

                                        @if($query)
                                            <!-- Highlight search terms (basic implementation) -->
                                            <div class="text-sm text-gray-600 mb-3">
                                                {!! Str::limit(strip_tags($question->question_text), 200) !!}
                                            </div>
                                        @endif

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
                        @endforeach

                        <!-- Pagination -->
                        @if($results->hasPages())
                            <div class="bg-white px-4 py-3 rounded-lg shadow-sm">
                                {{ $results->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection