<footer class="border-t border-(--color-border) bg-(--color-card) dark:border-white/10 dark:bg-(--color-card-dark)">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 sm:grid-cols-4">
            <div class="col-span-2 sm:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" class="h-7 w-auto">
                </a>
                <p class="mt-3 max-w-xs text-xs leading-relaxed text-(--color-text-secondary)">
                    Expert-led courses designed to help you master in-demand skills — one-time payment, lifetime access.
                </p>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-(--color-text-secondary)">Platform</h3>
                <ul class="mt-3 flex flex-col gap-2.5 text-sm">
                    <li><a href="{{ route('courses.index') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">Browse Courses</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">My Courses</a></li>
                        <li><a href="{{ route('profile') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">Account Settings</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">Register</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-(--color-text-secondary)">Support</h3>
                <ul class="mt-3 flex flex-col gap-2.5 text-sm">
                    <li><a href="{{ route('password.request') }}" class="text-(--color-text-secondary) transition-colors hover:text-(--color-primary)">Forgot Password</a></li>
                    <li><span class="text-(--color-text-secondary)">Payment help via admin</span></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wide text-(--color-text-secondary)">Trust</h3>
                <ul class="mt-3 flex flex-col gap-2.5 text-sm text-(--color-text-secondary)">
                    <li class="flex items-center gap-1.5"><x-icon name="shield" class="h-3.5 w-3.5 text-(--color-primary)" />Secure payments</li>
                    <li class="flex items-center gap-1.5"><x-icon name="check-circle" class="h-3.5 w-3.5 text-(--color-primary)" />Lifetime access</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-(--color-border) pt-6 text-xs text-(--color-text-secondary) sm:flex-row dark:border-white/10">
            <span>&copy; {{ date('Y') }} ArtiWeb. All rights reserved.</span>
            <span>Made for learners, by ArtiWeb.</span>
        </div>
    </div>
</footer>
