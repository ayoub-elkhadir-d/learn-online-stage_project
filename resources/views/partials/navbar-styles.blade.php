<style>
/* ═══════════════════════════════════════
   NAVBAR
═══════════════════════════════════════ */
.site-navbar {
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 1px 30px rgba(79,70,229,.08);
    padding: .6rem 0;
    transition: all .3s ease;
    z-index: 1000;
}
.site-navbar.scrolled {
    background: rgba(255,255,255,0.97);
    box-shadow: 0 4px 24px rgba(79,70,229,.12);
}
.site-navbar .navbar-brand img { transition: transform .3s; }
.site-navbar .navbar-brand:hover img { transform: scale(1.04); }

/* Nav links */
.site-navbar .nav-link {
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    padding: .45rem 1rem;
    border-radius: 10px;
    transition: all .2s;
    position: relative;
}
.site-navbar .nav-link::after {
    content: '';
    position: absolute;
    bottom: 4px; left: 50%;
    width: 0; height: 2px;
    background: linear-gradient(90deg,#4f46e5,#7c3aed);
    border-radius: 2px;
    transition: all .25s;
    transform: translateX(-50%);
}
.site-navbar .nav-link:hover { color: #4f46e5; background: rgba(79,70,229,.06); }
.site-navbar .nav-link:hover::after,
.site-navbar .nav-link.active::after { width: 60%; }
.site-navbar .nav-link.active { color: #4f46e5; font-weight: 600; }

/* User button */
.btn-user {
    background: linear-gradient(135deg,rgba(79,70,229,.08),rgba(124,58,237,.08));
    border: 1.5px solid rgba(79,70,229,.15);
    border-radius: 50px;
    padding: .3rem .6rem .3rem .35rem;
    font-size: 13px;
    font-weight: 600;
    color: #4f46e5;
    display: flex;
    align-items: center;
    gap: .5rem;
    transition: all .25s;
    cursor: pointer;
    white-space: nowrap;
}
.btn-user:hover {
    background: linear-gradient(135deg,rgba(79,70,229,.14),rgba(124,58,237,.14));
    border-color: rgba(79,70,229,.3);
    color: #4f46e5;
    box-shadow: 0 4px 16px rgba(79,70,229,.15);
}
.btn-user::after { display: none !important; }

/* Avatar */
.nav-avatar {
    width: 30px; height: 30px;
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    color: #fff;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(79,70,229,.35);
}

/* Dropdown */
.nav-dropdown {
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.12), 0 0 0 1px rgba(0,0,0,.04);
    padding: .5rem;
    min-width: 210px;
    background: #fff;
    margin-top: .5rem !important;
}
.nav-dropdown .dropdown-header {
    padding: .5rem .75rem .75rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: .25rem;
}
.nav-dropdown .d-name { font-size: 13px; font-weight: 700; color: #111827; }
.nav-dropdown .d-email { font-size: 11px; color: #9ca3af; margin-top: 1px; }
.nav-dropdown .dropdown-item {
    padding: .55rem .75rem;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    gap: .6rem;
    transition: all .15s;
}
.nav-dropdown .dropdown-item .di-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}
.nav-dropdown .dropdown-item:hover { background: #f5f3ff; color: #4f46e5; }
.nav-dropdown .dropdown-item:hover .di-icon { background: rgba(79,70,229,.12); }
.nav-dropdown .dropdown-item.text-danger:hover { background: #fff1f2; color: #dc2626; }
.nav-dropdown .dropdown-divider { margin: .35rem 0; border-color: #f3f4f6; }

/* Auth buttons */
.btn-nav-login {
    font-size: 13px; font-weight: 600;
    padding: .45rem 1.1rem;
    border-radius: 10px;
    border: 1.5px solid #e5e7eb;
    color: #374151;
    background: transparent;
    text-decoration: none;
    transition: all .2s;
    display: inline-flex; align-items: center;
}
.btn-nav-login:hover { border-color: #4f46e5; color: #4f46e5; background: rgba(79,70,229,.04); }
.btn-nav-register {
    font-size: 13px; font-weight: 600;
    padding: .45rem 1.2rem;
    border-radius: 10px;
    border: none;
    color: #fff;
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    text-decoration: none;
    transition: all .2s;
    display: inline-flex; align-items: center;
    box-shadow: 0 3px 12px rgba(79,70,229,.3);
}
.btn-nav-register:hover {
    opacity: .92;
    box-shadow: 0 5px 18px rgba(79,70,229,.4);
    color: #fff;
    transform: translateY(-1px);
}

/* Mobile toggler */
.navbar-toggler { border: none; padding: .35rem; }
.navbar-toggler:focus { box-shadow: none; }
.toggler-icon {
    display: flex; flex-direction: column; gap: 4px;
    width: 22px;
}
.toggler-icon span {
    display: block; height: 2px; border-radius: 2px;
    background: #4f46e5;
    transition: all .3s;
}

/* My Courses link */
.btn-my-courses {
    font-size: 13px; font-weight: 600;
    padding: .45rem .9rem;
    border-radius: 10px;
    color: #4f46e5;
    background: rgba(79,70,229,.06);
    text-decoration: none;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: all .2s;
}
.btn-my-courses:hover { background: rgba(79,70,229,.12); color: #4f46e5; }
.btn-my-courses.active { background: rgba(79,70,229,.12); }
</style>
