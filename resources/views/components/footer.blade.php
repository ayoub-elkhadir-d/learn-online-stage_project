<footer class="border-t border-(--color-border) bg-(--color-card) dark:border-white/10 dark:bg-(--color-card-dark)">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 sm:grid-cols-4">
            <div class="col-span-2 sm:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" class="h-7 w-auto">
                </a>
                <p class="mt-3 max-w-xs text-xs leading-relaxed text-(--color-text-secondary)">
                    {{ __('common.footer_tagline') }}
                </p>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-(--color-text-secondary)">{{ __('common.footer_platform') }}</h3>
                <ul class="mt-3 flex flex-col gap-2.5 text-sm">
                    <li><a href="{{ route('courses.index') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">{{ __('common.footer_browse_courses') }}</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">{{ __('common.footer_my_courses') }}</a></li>
                        <li><a href="{{ route('profile') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">{{ __('common.footer_account_settings') }}</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">{{ __('common.footer_login') }}</a></li>
                        <li><a href="{{ route('register') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">{{ __('common.footer_register') }}</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-(--color-text-secondary)">{{ __('common.footer_support') }}</h3>
                <ul class="mt-3 flex flex-col gap-2.5 text-sm">
                    <li><a href="{{ route('password.request') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">{{ __('common.footer_forgot_password') }}</a></li>
                    <li><span class="text-(--color-text-secondary)">{{ __('common.footer_payment_help') }}</span></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-(--color-text-secondary)">{{ __('common.footer_trust') }}</h3>
                <ul class="mt-3 flex flex-col gap-2.5 text-sm text-(--color-text-secondary)">
                    <li class="flex items-center gap-1.5"><x-icon name="shield" class="h-3.5 w-3.5 text-(--color-primary)" />{{ __('common.footer_secure_payments') }}</li>
                    <li class="flex items-center gap-1.5"><x-icon name="check-circle" class="h-3.5 w-3.5 text-(--color-primary)" />{{ __('common.footer_lifetime_access') }}</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-(--color-border) pt-6 text-xs text-(--color-text-secondary) sm:flex-row dark:border-white/10">
            <span>{{ __('common.footer_rights', ['year' => date('Y')]) }}</span>
            <span>{{ __('common.footer_made_for') }}</span>
        </div>
    </div>
</footer>
