@extends('layouts.app')

@section('title', 'My Bookmarks')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Bookmarks</h1>
                    <p class="mt-2 text-gray-600">Your saved questions for quick review</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('question-bank.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Browse Questions
                    </a>
                    <a href="{{ route('question-bank.practice') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Practice Mode
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($bookmarks->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-bookmark text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bookmarks yet</h3>
                <p class="text-gray-600 mb-6">Start bookmarking questions you want to review later.</p>
                <a href="{{ route('question-bank.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Questions
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($bookmarks as $bookmark)
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($bookmark->question->difficulty_level === 'easy') bg-green-100 text-green-800
                                    @elseif($bookmark->question->difficulty_level === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($bookmark->question->difficulty_level->value) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $bookmark->question->chapter->topic->subject->name }} >
                                    {{ $bookmark->question->chapter->topic->name }} >
                                    {{ $bookmark->question->chapter->name }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Bookmarked {{ $bookmark->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {!! Str::limit(strip_tags($bookmark->question->question_text), 120) !!}
                            </h3>
                            <div class="text-sm text-gray-500">
                                <span><i class="fas fa-eye mr-1"></i>{{ $bookmark->question->views_count }} views</span>
                                <span class="ml-4"><i class="fas fa-check-circle mr-1"></i>{{ $bookmark->question->attempts_count }} attempts</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <a href="{{ route('question-bank.show', $bookmark->question_id) }}"
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    <i class="fas fa-play mr-1"></i>Practice
                                </a>
                                <a href="{{ route('question-bank.chapter', $bookmark->question->chapter_id) }}"
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    <i class="fas fa-list mr-1"></i>Chapter
                                </a>
                            </div>
                            <form method="POST" action="{{ route('question-bank.toggle-bookmark', $bookmark->question_id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    <i class="fas fa-trash mr-1"></i>Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bookmarks->hasPages())
                <div class="mt-8 bg-white px-4 py-3 rounded-lg shadow-sm">
                    {{ $bookmarks->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection