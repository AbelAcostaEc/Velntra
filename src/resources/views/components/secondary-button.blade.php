<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-primary-200 bg-white px-4 text-sm font-semibold text-primary-700 shadow-sm transition hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
