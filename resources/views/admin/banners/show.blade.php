@extends('layouts.admin')

@section('title', $banner->title)

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.banners.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Banners</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.banners.edit', $banner->id) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Edit</a>
            <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: #dc2626; color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Delete</button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); margin-bottom: var(--spacing-4);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">{{ $banner->title }}</h1>

        <div style="display: grid; gap: var(--spacing-3);">
            @if($banner->image_path)
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Image</p>
                    <img src="{{ Storage::url($banner->image_path) }}" alt="{{ $banner->title }}" style="max-width: 100%; border-radius: var(--radius-lg);">
                </div>
            @endif

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">URL</p>
                <p style="margin: 0; color: var(--color-gray-900); word-break: break-all;">
                    @if($banner->url)
                        <a href="{{ $banner->url }}" target="_blank" style="color: var(--color-primary); text-decoration: none;">{{ $banner->url }}</a>
                    @else
                        <span style="color: var(--color-gray-500);">—</span>
                    @endif
                </p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Position</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ ucfirst($banner->position ?? 'top') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Status</p>
                <p style="margin: 0;">
                    <span style="background-color: {{ $banner->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $banner->is_active ? '#166534' : '#991b1b' }}; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); display: inline-block;">
                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>

            @if($banner->description)
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Description</p>
                    <p style="margin: 0; color: var(--color-gray-900);">{{ $banner->description }}</p>
                </div>
            @endif

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Created At</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $banner->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Last Updated</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $banner->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
