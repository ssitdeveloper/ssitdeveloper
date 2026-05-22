@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Banners</h1>
        <a href="{{ route('admin.banners.create') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">+ Create Banner</a>
    </div>

    @if ($banners->isEmpty())
        <div class="card" style="text-align: center; padding: var(--spacing-8); background-color: var(--color-gray-50);">
            <p style="margin: 0; color: var(--color-gray-500);">No banners found. Create one to get started.</p>
        </div>
    @else
        <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Title</th>
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">URL</th>
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Status</th>
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Position</th>
                        <th style="text-align: center; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($banners as $banner)
                        <tr style="border-bottom: 1px solid var(--color-gray-100);">
                            <td style="padding: var(--spacing-3); color: var(--color-gray-900);">{{ $banner->title }}</td>
                            <td style="padding: var(--spacing-3); color: var(--color-gray-700); word-break: break-all;">{{ Str::limit($banner->url ?? '', 40) }}</td>
                            <td style="padding: var(--spacing-3);">
                                <span style="background-color: {{ $banner->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $banner->is_active ? '#166534' : '#991b1b' }}; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--radius-lg); font-size: var(--font-size-xs); font-weight: var(--font-weight-medium); display: inline-block;">
                                    {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="padding: var(--spacing-3); color: var(--color-gray-700);">{{ $banner->position ?? 'top' }}</td>
                            <td style="padding: var(--spacing-3); text-align: center;">
                                <div style="display: flex; justify-content: center; gap: var(--spacing-2);">
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" style="padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Edit</a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: var(--spacing-1) var(--spacing-2); background-color: #dc2626; color: white; border: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm); cursor: pointer;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
