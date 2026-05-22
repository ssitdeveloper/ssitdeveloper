@extends('layouts.admin')

@section('title', 'Create Banner')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.banners.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Banners</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); max-width: 600px;">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Create New Banner</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <ul style="margin: 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" style="display: grid; gap: var(--spacing-4);">
            @csrf

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Title <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Image <span style="color: #dc2626;">*</span></label>
                <input type="file" name="image" accept="image/*" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Recommended: 1200×300px or similar aspect ratio</p>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Display Type <span style="color: #dc2626;">*</span></label>
                <select name="display_type" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <option value="">Select display type</option>
                    <option value="horizontal">Horizontal (1200×300px)</option>
                    <option value="vertical">Vertical (400×600px)</option>
                    <option value="popup">Popup (600×400px)</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">URL (Link destination)</label>
                <input type="url" name="url" placeholder="https://example.com" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Position</label>
                <select name="position" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <option value="top">Top</option>
                    <option value="bottom">Bottom</option>
                    <option value="sidebar">Sidebar</option>
                </select>
            </div>

            <div>
                <label style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" checked style="cursor: pointer;">
                    <span style="font-weight: var(--font-weight-medium);">Active</span>
                </label>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Description</label>
                <textarea name="description" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box; min-height: 100px;"></textarea>
            </div>

            <div style="display: flex; gap: var(--spacing-2);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Create</button>
                <a href="{{ route('admin.banners.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
