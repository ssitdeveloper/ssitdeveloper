@extends('layouts.app')

@section('title', 'Chapter Questions')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $chapter->name }}</h1>
                    <p class="mt-2 text-gray-600">
                        {{ $chapter->topic->subject->name }} > {{ $chapter->topic->name }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('question-bank.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Question Bank
                    </a>
                    <a href="{{ route('question-bank.practice', ['chapter_id' => $chapter->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Practice This Chapter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Chapter Info Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Chapter Overview</h3>

                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Total Questions:</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $chapter->questions()->published()->count() }}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-700">Difficulty Breakdown:</span>
                            <div class="mt-2 space-y-1">
                                @php
                                    $easyCount = $chapter->questions()->published()->where('difficulty_level', 'easy')->count();
                                    $mediumCount = $chapter->questions()->published()->where('difficulty_level', 'medium')->count();
                                    $hardCount = $chapter->questions()->published()->where('difficulty_level', 'hard')->count();
                                    $total = $easyCount + $mediumCount + $hardCount;
                                @endphp

                                <div class="flex justify-between text-sm">
                                    <span class="text-green-600">Easy:</span>
                                    <span>{{ $easyCount }} ({{ $total > 0 ? round(($easyCount/$total)*100) : 0 }}%)</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-yellow-600">Medium:</span>
                                    <span>{{ $mediumCount }} ({{ $total > 0 ? round(($mediumCount/$total)*100) : 0 }}%)</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-red-600">Hard:</span>
                                    <span>{{ $hardCount }} ({{ $total > 0 ? round(($hardCount/$total)*100) : 0 }}%)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Filter Questions</h4>

                            <form method="GET" class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Difficulty</label>
                                    <select name="difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Levels</option>
                                        <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                                        <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Limit</label>
                                    <select name="limit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Questions</option>
                                        <option value="10" {{ request('limit') == '10' ? 'selected' : '' }}>10 Questions</option>
                                        <option value="25" {{ request('limit') == '25' ? 'selected' : '' }}>25 Questions</option>
                                        <option value="50" {{ request('limit') == '50' ? 'selected' : '' }}>50 Questions</option>
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="random" value="1" id="random-checkbox"
                                           {{ request('random') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="random-checkbox" class="ml-2 text-sm text-gray-600">Random order</label>
                                </div>

                                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                    Apply Filters
                                </button>
                            </form>
                        </div>
                    </div>
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
                                            Question #{{ $question->id }}
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
                            <p class="text-gray-600">Try adjusting your filters or check back later.</p>
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
@endsection