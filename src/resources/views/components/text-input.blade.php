@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-primary-200 bg-white text-sm text-primary-900 shadow-sm placeholder:text-primary-400 focus:border-accent-500 focus:ring-accent-500 disabled:cursor-not-allowed disabled:bg-primary-100 disabled:text-primary-500']) }}>
