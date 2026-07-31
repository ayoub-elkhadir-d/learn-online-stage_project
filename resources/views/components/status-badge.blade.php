@props(['status'])

@php
$map = [
    'pending'   => ['label' => 'Pending',   'icon' => 'clock',        'class' => 'bg-(--color-warning)/10 text-(--color-warning)'],
    'paid'      => ['label' => 'Approved',  'icon' => 'check-circle', 'class' => 'bg-(--color-success)/10 text-(--color-success)'],
    'rejected'  => ['label' => 'Rejected',  'icon' => 'x',            'class' => 'bg-(--color-danger)/10 text-(--color-danger)'],
    'cancelled' => ['label' => 'Cancelled', 'icon' => 'ban',          'class' => 'bg-(--color-text)/8 text-(--color-text-secondary) dark:bg-white/10'],
];
$s = $map[$status] ?? ['label' => ucfirst($status), 'icon' => 'circle', 'class' => 'bg-(--color-text)/8 text-(--color-text-secondary) dark:bg-white/10'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold '.$s['class']]) }}>
    <x-icon :name="$s['icon']" class="h-3.5 w-3.5" />
    {{ $s['label'] }}
</span>
