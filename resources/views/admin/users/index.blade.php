@extends('admin.layout')
@section('title', 'Users')

@section('content')

<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
    <div class="lesson-card p-5 text-center">
        <div class="text-2xl font-extrabold text-(--color-text) dark:text-white">{{ $stats['total'] }}</div>
        <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Total Users</div>
    </div>
    <div class="lesson-card p-5 text-center">
        <div class="text-2xl font-extrabold text-(--color-success)">{{ $stats['active'] }}</div>
        <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Active</div>
    </div>
    <div class="lesson-card p-5 text-center">
        <div class="text-2xl font-extrabold text-(--color-danger)">{{ $stats['banned'] }}</div>
        <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Banned</div>
    </div>
    <div class="lesson-card p-5 text-center">
        <div class="text-2xl font-extrabold text-(--color-warning)">{{ $stats['with_courses'] }}</div>
        <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">With Courses</div>
    </div>
</div>

<div class="mb-5 mt-6 flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-lg font-bold text-(--color-text) dark:text-white">All Users</h2>
    <span class="rounded-full bg-(--color-text)/8 px-2.5 py-1 text-xs font-semibold text-(--color-text-secondary) dark:bg-white/10">{{ $users->total() }} {{ Str::plural('user', $users->total()) }}</span>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="mb-5">
    <div class="relative max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name or email..." class="input-field pl-9">
    </div>
</form>

@if($users->isEmpty())
    <div class="lesson-card flex flex-col items-center px-6 py-16 text-center">
        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
            <x-icon name="users" class="h-7 w-7" />
        </span>
        <h3 class="text-base font-bold text-(--color-text) dark:text-white">No users found</h3>
        <p class="mt-1 max-w-sm text-sm text-(--color-text-secondary)">@if(request('q')) Try a different search term. @else Registered students will appear here. @endif</p>
    </div>
@else

    {{-- Desktop / tablet table --}}
    <div class="lesson-card hidden overflow-x-auto md:block">
        <table class="admin-table w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">User</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Joined</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Courses</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Status</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-t border-(--color-border) transition-colors hover:bg-black/[.02] dark:border-white/10 dark:hover:bg-white/[.03]">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $user->name }}</div>
                                    <div class="truncate text-xs text-(--color-text-secondary)">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-xs text-(--color-text-secondary)">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 rounded-full bg-(--color-primary)/10 px-2.5 py-1 text-xs font-semibold text-(--color-primary)">
                                <x-icon name="book-open" class="h-3 w-3" />
                                {{ $user->purchases_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($user->isBanned())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-(--color-danger)/10 px-2.5 py-1 text-xs font-semibold text-(--color-danger)">
                                    <x-icon name="ban" class="h-3.5 w-3.5" />
                                    Banned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-(--color-success)/10 px-2.5 py-1 text-xs font-semibold text-(--color-success)">
                                    <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            @if($user->isBanned())
                                <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                    @csrf @method('PUT')
                                    <button class="inline-flex items-center gap-1.5 rounded-xl bg-(--color-success) px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:opacity-90">
                                        <x-icon name="check" class="h-3.5 w-3.5" />
                                        Unban
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                                      onsubmit="return confirm('Ban user {{ $user->name }}? They will lose access.')">
                                    @csrf @method('PUT')
                                    <button class="inline-flex items-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                        <x-icon name="ban" class="h-3.5 w-3.5" />
                                        Ban
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile card list --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($users as $user)
            <div class="lesson-card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $user->name }}</div>
                            <div class="truncate text-xs text-(--color-text-secondary)">{{ $user->email }}</div>
                        </div>
                    </div>
                    @if($user->isBanned())
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-(--color-danger)/10 px-2.5 py-1 text-xs font-semibold text-(--color-danger)">
                            <x-icon name="ban" class="h-3.5 w-3.5" />Banned
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-(--color-success)/10 px-2.5 py-1 text-xs font-semibold text-(--color-success)">
                            <x-icon name="check-circle" class="h-3.5 w-3.5" />Active
                        </span>
                    @endif
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-(--color-text-secondary)">
                    <span>Joined {{ $user->created_at->format('d M Y') }}</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-(--color-primary)/10 px-2 py-0.5 font-semibold text-(--color-primary)">
                        <x-icon name="book-open" class="h-3 w-3" />{{ $user->purchases_count }}
                    </span>
                </div>
                <div class="mt-3">
                    @if($user->isBanned())
                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                            @csrf @method('PUT')
                            <button class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-(--color-success) px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:opacity-90">
                                <x-icon name="check" class="h-3.5 w-3.5" />Unban
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                              onsubmit="return confirm('Ban user {{ $user->name }}? They will lose access.')">
                            @csrf @method('PUT')
                            <button class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                <x-icon name="ban" class="h-3.5 w-3.5" />Ban
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endif

@endsection
