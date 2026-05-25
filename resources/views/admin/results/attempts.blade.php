@extends('layouts.admin')

@section('title', 'Test Attempts')

@section('content')
<div class="admin-content">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Test Attempts</h1>
        <p class="text-gray-600 mt-2">View and manage all student test attempts</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.results.attempts') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">Student</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
                    <option value="">All Students</option>
                    @foreach($students ?? [] as $student)
                        <option value="{{ $student->id }}" {{ request('user_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }} ({{ $student->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="test_id" class="block text-sm font-medium text-gray-700">Test</label>
                <select name="test_id" id="test_id" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
                    <option value="">All Tests</option>
                    @foreach($tests ?? [] as $test)
                        <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                            {{ $test->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                </select>
            </div>

            <div>
                <label for="score_min" class="block text-sm font-medium text-gray-700">Min Score (%)</label>
                <input type="number" name="score_min" id="score_min" min="0" max="100" value="{{ request('score_min') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div>
                <label for="score_max" class="block text-sm font-medium text-gray-700">Max Score (%)</label>
                <input type="number" name="score_max" id="score_max" min="0" max="100" value="{{ request('score_max') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.results.attempts') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Student</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Test</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Score</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($attempts ?? [] as $attempt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>
                                    <p class="font-medium">{{ $attempt->user->name }}</p>
                                    <p class="text-gray-600">{{ $attempt->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $attempt->test->title }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="font-semibold {{ $attempt->score >= 75 ? 'text-green-600' : ($attempt->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($attempt->score ?? 0, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{
                                    $attempt->status === 'completed' ? 'bg-green-100 text-green-800' :
                                    ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $attempt->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.results.attempt', $attempt) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-600">
                                No attempts found. Try adjusting your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white border-t px-6 py-4">
            {{ $attempts->links() ?? '' }}
        </div>
    </div>
</div>
@endsection
