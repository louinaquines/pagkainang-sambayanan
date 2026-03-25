<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pagkainang Sambayanan') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --red:     #B02818;
            --red2:    #CC3020;
            --red3:    #E04030;
            --gold:    #C49010;
            --gold2:   #DCA820;
            --gold3:   #F0C840;
            --dark:    #160800;
            --dark2:   #1E0C02;
            --dark3:   #2A1206;
            --brown:   #583018;
            --text:    #260C00;
            --muted:   #7A6050;
            --light:   #F6EED8;
            --lighter: #FBF5E8;
            --white:   #FFFDF8;
            --green:   #1bb613;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Jost', sans-serif;
            background: var(--lighter);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .serif { font-family: 'Cormorant Garamond', serif; }

        .page-shell { min-height: 100vh; display: flex; flex-direction: column; }

        main {
            flex: 1;
            max-width: 1320px; margin: 0 auto;
            width: 100%; padding: 48px 40px 96px;
        }

        /* TOP INFO BAR */
        .top-bar {
            background: var(--dark);
            padding: 9px 40px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .top-bar-text { font-size: 11px; color: rgba(255,255,255,0.4); letter-spacing: 1px; }
        .top-bar-gold { color: var(--gold3); font-weight: 600; }

        .emergency-banner {
            background: var(--red); color: white; text-align: center;
            padding: 10px 20px; font-size: 11px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            border-bottom: 2px solid var(--gold2);
        }

        /* MAIN NAV */
        .main-nav {
            background: var(--white);
            border-bottom: 1px solid rgba(196,144,16,0.15);
            position: sticky; top: 0; z-index: 200;
            box-shadow: 0 2px 20px rgba(22,8,0,0.05);
        }
        .nav-inner {
            max-width: 1320px; margin: 0 auto; padding: 0 40px;
            height: 72px; display: flex; align-items: center; justify-content: space-between;
        }
        .nav-logo { text-decoration: none; display: flex; align-items: center; gap: 12px; }
        .nav-logo-icon {
            width: 44px; height: 44px; border-radius: 10px;
            background: linear-gradient(145deg, var(--red), var(--red2));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(176,40,24,0.28);
        }
        .nav-logo-main { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 700; color: var(--dark); line-height: 1; }
        .nav-logo-sub  { font-size: 8.5px; font-weight: 600; color: var(--gold); letter-spacing: 3.5px; text-transform: uppercase; margin-top: 3px; }

        .nav-links { display: flex; align-items: center; gap: 2px; margin-left: 7rem; }
        .nav-link {
            padding: 8px 14px; font-size: 13px; font-weight: 500;
            color: var(--brown); text-decoration: none; letter-spacing: 0.3px;
            position: relative; transition: color 0.2s; white-space: nowrap;
            font-family: 'Jost', sans-serif; line-height: 1;
        }
        .nav-link::after {
            content: ''; position: absolute; bottom: -1px; left: 14px; right: 14px;
            height: 2px; background: var(--red);
            transform: scaleX(0); transition: transform 0.25s ease;
        }
        .nav-link:hover { color: var(--red); }
        .nav-link:hover::after, .nav-link.active::after { transform: scaleX(1); }
        .nav-link.active { color: var(--red); font-weight: 600; }
        .nav-link.nav-admin { color: var(--brown); font-weight: 600; margin-left: 0.5rem; padding: 8px 14px;}
        .nav-link.nav-admin::after { background: var(--gold); }
        .nav-link.nav-admin:hover { color: var(--gold2); }
        .nav-link.nav-admin.active { color: var(--gold2); }
        button.nav-link {
            font-size: 13px; font-weight: 600; font-family: 'Jost', sans-serif;
            letter-spacing: 0.3px; line-height: 1; color: var(--gold);
        }
        button.nav-link:hover { color: var(--gold2); }

        /* Mobile hamburger */
        .nav-menu-toggle {
            display: none;
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid rgba(196,144,16,0.3); background: var(--white);
            align-items: center; justify-content: center;
            cursor: pointer; flex-direction: column; gap: 5px;
            transition: all 0.2s ease;
        }
        .nav-menu-toggle span {
            display: block; width: 16px; height: 1.8px;
            border-radius: 999px; background: var(--brown);
            transition: transform 0.22s ease, opacity 0.22s ease;
        }
        .nav-menu-toggle.is-open span:nth-child(1) { transform: translateY(6.8px) rotate(45deg); }
        .nav-menu-toggle.is-open span:nth-child(2) { opacity: 0; }
        .nav-menu-toggle.is-open span:nth-child(3) { transform: translateY(-6.8px) rotate(-45deg); }

        .nav-user-wrap { position: relative; }
        .nav-user-btn {
            display: flex; align-items: center; gap: 10px;
            background: var(--lighter); border: 1px solid rgba(196,144,16,0.2);
            border-radius: 99px; padding: 5px 16px 5px 5px;
            cursor: pointer; font-family: 'Jost', sans-serif; transition: all 0.2s;
        }
        .nav-user-btn:hover { border-color: var(--gold); background: var(--light); }
        .nav-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(145deg, var(--red), var(--red2));
            color: white; font-size: 13px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(176,40,24,0.22);
        }
        .nav-user-name { font-size: 13px; font-weight: 600; color: var(--text); }

        .nav-dropdown {
            position: absolute; top: calc(100% + 12px); right: 0;
            background: var(--white); border-radius: 16px; min-width: 220px;
            box-shadow: 0 4px 8px rgba(22,8,0,0.04), 0 20px 60px rgba(22,8,0,0.14);
            border: 1px solid rgba(196,144,16,0.12);
            overflow: hidden; z-index: 400;
            animation: dropIn 0.2s ease;
        }
        .nav-dropdown-head {
            padding: 16px 20px;
            background: linear-gradient(135deg, var(--lighter), var(--light));
            border-bottom: 1px solid rgba(196,144,16,0.12);
        }
        .nav-dd-name  { font-size: 14px; font-weight: 700; color: var(--dark); }
        .nav-dd-role  { font-size: 11px; color: var(--muted); margin-top: 3px; text-transform: capitalize; letter-spacing: 0.5px; }
        .nav-dd-email { font-size: 11px; color: rgba(122,96,80,0.55); margin-top: 2px; }
        .nav-dd-item {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px; font-size: 13px; font-weight: 500; color: var(--brown);
            text-decoration: none; transition: background 0.15s;
            border: none; background: none; cursor: pointer; width: 100%;
            font-family: 'Jost', sans-serif;
        }
        .nav-dd-item:hover { background: rgba(196,144,16,0.07); }
        .nav-dd-item.dd-logout { color: var(--red); border-top: 1px solid rgba(196,144,16,0.1); }
        .nav-dd-item.dd-logout:hover { background: rgba(176,40,24,0.06); }

        /* PAGE HEADER */
        .page-title-bar {
            background: var(--white);
            border-bottom: 1px solid rgba(196,144,16,0.1);
        }
        .page-title-inner {
            max-width: 1320px; margin: 0 auto; padding: 22px 40px;
            display: flex; align-items: center; gap: 16px;
        }
        .page-title-bar h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px; font-weight: 700; color: var(--dark); white-space: nowrap;
        }
        .page-title-line { flex: 1; height: 1px; background: linear-gradient(90deg, rgba(196,144,16,0.25), transparent); }

        /* CARDS */
        .card {
            background: var(--white); border-radius: 16px;
            border: 1px solid rgba(196,144,16,0.12);
            box-shadow: 0 1px 3px rgba(22,8,0,0.03), 0 6px 20px rgba(22,8,0,0.05);
            transition: box-shadow 0.3s, transform 0.3s, border-color 0.3s;
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(22,8,0,0.06), 0 20px 50px rgba(22,8,0,0.09);
            transform: translateY(-3px); border-color: rgba(196,144,16,0.25);
        }

        .don-card {
            background: var(--white); border-radius: 16px;
            border: 1px solid rgba(196,144,16,0.1);
            box-shadow: 0 2px 8px rgba(22,8,0,0.04), 0 8px 28px rgba(22,8,0,0.05);
            overflow: hidden; transition: all 0.35s ease;
        }
        .don-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 36px rgba(176,40,24,0.12), 0 32px 72px rgba(22,8,0,0.09);
            border-color: rgba(196,144,16,0.28);
        }
        .don-card-header {
            background: linear-gradient(135deg, var(--lighter), var(--light));
            padding: 20px 24px; border-bottom: 1px solid rgba(196,144,16,0.1);
            position: relative; overflow: hidden;
        }
        .don-card-header::after {
            content: ''; position: absolute; top: -10px; right: -10px;
            width: 70px; height: 70px; border-radius: 50%;
            background: radial-gradient(circle, rgba(196,144,16,0.1) 0%, transparent 70%);
        }
        .don-card-body { padding: 18px 24px 22px; }

        .stat-card {
            background: var(--white); border-radius: 16px; padding: 32px 24px; text-align: center;
            border: 1px solid rgba(196,144,16,0.1);
            box-shadow: 0 2px 8px rgba(22,8,0,0.04), 0 6px 24px rgba(22,8,0,0.04);
            transition: all 0.3s ease; position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold3));
            transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 32px rgba(22,8,0,0.1); }
        .stat-card:hover::before { transform: scaleX(1); }
        .stat-num { font-family: 'Cormorant Garamond', serif; font-size: 52px; font-weight: 700; line-height: 1; }
        .stat-lbl { font-size: 10px; color: var(--muted); margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 8px; padding: 13px 32px; border-radius: 8px;
            font-weight: 600; font-size: 12px; letter-spacing: 1px; text-transform: uppercase;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.25s ease; font-family: 'Jost', sans-serif;
            white-space: nowrap;
        }
        .btn-red    { background: var(--red); color: white; box-shadow: 0 4px 16px rgba(176,40,24,0.25); }
        .btn-red:hover    { background: var(--red2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(176,40,24,0.35); color: white; text-decoration: none; }
        .btn-gold   { background: var(--gold); color: var(--dark); font-weight: 700; box-shadow: 0 4px 16px rgba(196,144,16,0.25); }
        .btn-gold:hover   { background: var(--gold2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(196,144,16,0.4); color: var(--dark); text-decoration: none; }
        .btn-dark   { background: var(--dark); color: white; box-shadow: 0 4px 16px rgba(22,8,0,0.25); }
        .btn-dark:hover   { background: var(--dark3); transform: translateY(-2px); color: white; text-decoration: none; }
        .btn-green  { background: #1A7A40; color: white; box-shadow: 0 4px 16px rgba(26,122,64,0.25); }
        .btn-green:hover  { background: #218A48; transform: translateY(-2px); color: white; text-decoration: none; box-shadow: 0 8px 24px rgba(26,122,64,0.35); }
        .btn-ghost  { background: transparent; color: var(--brown); border: 1.5px solid rgba(196,144,16,0.3); }
        .btn-ghost:hover  { border-color: var(--gold); color: var(--text); background: rgba(196,144,16,0.06); transform: translateY(-2px); text-decoration: none; }
        .btn-outline-red { background: transparent; color: var(--red); border: 1.5px solid var(--red); }
        .btn-outline-red:hover { background: var(--red); color: white; transform: translateY(-2px); text-decoration: none; }

        /* BADGES */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 99px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;
        }
        .badge-green  { background: #E4F5EB; color: #186634; }
        .badge-yellow { background: #FDF5DC; color: #7A5500; }
        .badge-blue   { background: #E4EDF8; color: #193A8A; }
        .badge-red    { background: #F8E4E2; color: #8A1A14; }
        .badge-muted  { background: rgba(122,96,80,0.1); color: var(--muted); }

        /* ALERTS */
        .alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; display: flex; align-items: center; gap: 12px; border-left: 3px solid; }
        .alert-success { background: #E4F5EB; border-color: #1A7A40; color: #186634; }
        .alert-error   { background: #F8E4E2; border-color: var(--red); color: #8A1A14; }
        .alert-warning { background: #FDF5DC; border-color: var(--gold); color: #7A5500; }

        /* TABLE */
        .ps-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 12px; }
        .ps-table { width: 100%; border-collapse: collapse; }
        .ps-table thead { background: var(--dark); }
        .ps-table thead th { padding: 14px 22px; font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.6); text-align: left; }
        .ps-table thead th:first-child { border-radius: 10px 0 0 10px; }
        .ps-table thead th:last-child  { border-radius: 0 10px 10px 0; }
        .ps-table tbody tr { transition: background 0.15s; border-bottom: 1px solid rgba(196,144,16,0.08); }
        .ps-table tbody tr:hover { background: rgba(196,144,16,0.04); }
        .ps-table tbody td { padding: 15px 22px; font-size: 14px; color: var(--text); vertical-align: middle; }

        /* FORMS */
        .form-group { margin-bottom: 22px; }
        .form-label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); margin-bottom: 8px; letter-spacing: 1.5px; text-transform: uppercase; }
        .form-input {
            width: 100%; padding: 13px 16px; border-radius: 10px;
            border: 1.5px solid rgba(196,144,16,0.2); font-size: 14px;
            font-family: 'Jost', sans-serif; background: var(--lighter); color: var(--text);
            transition: all 0.2s; outline: none;
        }
        .form-input:focus { border-color: var(--red); background: var(--white); box-shadow: 0 0 0 4px rgba(176,40,24,0.08); }
        .form-input::placeholder { color: rgba(122,96,80,0.4); }
        .form-error { font-size: 12px; color: var(--red); margin-top: 6px; font-weight: 500; }

        /* DECORATIVE */
        .gold-rule { height: 1px; border: none; background: linear-gradient(90deg, transparent, rgba(196,144,16,0.35) 30%, var(--gold2) 50%, rgba(196,144,16,0.35) 70%, transparent); }
        .section-label { display: inline-flex; align-items: center; gap: 10px; font-size: 11px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); margin-bottom: 14px; }
        .section-label::before { content: ''; width: 32px; height: 1.5px; background: var(--gold); display: block; }

        /* TOAST */
        #toast-container { position: fixed; top: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; pointer-events: none; }
        .ps-toast {
            display: flex; align-items: flex-start; gap: 14px;
            background: var(--white); border-radius: 14px; padding: 16px 18px;
            min-width: 320px; max-width: 380px;
            box-shadow: 0 4px 8px rgba(22,8,0,0.06), 0 20px 60px rgba(22,8,0,0.16);
            border: 1px solid rgba(196,144,16,0.14); border-left: 4px solid var(--red);
            pointer-events: all; animation: toastIn 0.45s cubic-bezier(0.175,0.885,0.32,1.275) both;
            position: relative; overflow: hidden;
        }
        .ps-toast::after { content: ''; position: absolute; bottom: 0; left: 0; height: 2px; animation: toastBar 4.5s linear forwards; width: 100%; }
        .ps-toast.t-success { border-left-color: #1A7A40; }
        .ps-toast.t-success::after { background: linear-gradient(90deg, #1A7A40, #25A855); }
        .ps-toast.t-error::after   { background: linear-gradient(90deg, var(--red), var(--red3)); }
        .ps-toast.t-warning { border-left-color: var(--gold); }
        .ps-toast.t-warning::after { background: linear-gradient(90deg, var(--gold), var(--gold3)); }
        .t-icon { width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .t-success .t-icon { background: linear-gradient(135deg, #1A7A40, #25A855); }
        .t-error   .t-icon { background: linear-gradient(135deg, var(--red), var(--red2)); }
        .t-warning .t-icon { background: linear-gradient(135deg, var(--gold), var(--gold2)); }
        .t-body { flex: 1; }
        .t-title { font-weight: 700; font-size: 14px; color: var(--dark); margin-bottom: 3px; }
        .t-msg   { font-size: 12px; color: var(--muted); line-height: 1.5; }
        .t-close { background: none; border: none; cursor: pointer; color: rgba(196,144,16,0.3); font-size: 14px; padding: 2px; flex-shrink: 0; transition: color 0.2s; line-height: 1; }
        .t-close:hover { color: var(--muted); }
        .ps-toast.t-out { animation: toastOut 0.3s ease forwards; }

        /* ANIMATIONS */
        @keyframes toastIn  { from { opacity:0; transform:translateX(60px) scale(0.9); } to { opacity:1; transform:translateX(0) scale(1); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(60px); } }
        @keyframes toastBar { from { width:100%; } to { width:0%; } }
        @keyframes dropIn   { from { opacity:0; transform:translateY(-8px) scale(0.97); } to { opacity:1; transform:translateY(0) scale(1); } }

        main { animation: pageIn 0.4s ease both; }
        @keyframes pageIn { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

        .stagger > * { animation: fadeUp 0.5s ease both; }
        .stagger > *:nth-child(1) { animation-delay:0.04s; }
        .stagger > *:nth-child(2) { animation-delay:0.10s; }
        .stagger > *:nth-child(3) { animation-delay:0.16s; }
        .stagger > *:nth-child(4) { animation-delay:0.22s; }
        .stagger > *:nth-child(5) { animation-delay:0.28s; }
        .stagger > *:nth-child(6) { animation-delay:0.34s; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        /* FOOTER */
        .main-footer { background: var(--dark); color: white; border-top: 1px solid rgba(196,144,16,0.2); }
        .footer-top { max-width: 1320px; margin: 0 auto; padding: 52px 40px 40px; display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 48px; }
        .footer-brand { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 700; color: var(--gold3); margin-bottom: 10px; }
        .footer-desc  { font-size: 13px; color: rgba(255,255,255,0.32); line-height: 1.8; max-width: 280px; }
        .footer-col-title { font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--gold); margin-bottom: 18px; }
        .footer-link { display: block; font-size: 13px; color: rgba(255,255,255,0.38); text-decoration: none; margin-bottom: 10px; transition: color 0.2s; }
        .footer-link:hover { color: rgba(255,255,255,0.75); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.06); padding: 18px 40px; display: flex; align-items: center; justify-content: space-between; max-width: 1320px; margin: 0 auto; }
        .footer-copy { font-size: 12px; color: rgba(255,255,255,0.22); }
        .footer-sdg  { font-size: 11px; color: rgba(196,144,16,0.55); letter-spacing: 0.5px; }

        /* ═══════════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ════════════════════════════════════════ */

        /* Tablet: 768–1024px */
        @media (max-width: 1024px) {
            main { padding: 36px 28px 80px; }
            .nav-inner { padding: 0 28px; }
            .page-title-inner { padding: 18px 28px; }
            .page-title-bar h2 { font-size: 24px; }
            
            /* Stat cards - 2 columns */
            .stat-card { padding: 24px 16px; }
            .stat-num { font-size: 40px; }
            
            /* Grid adjustments */
            .footer-top { grid-template-columns: 1fr 1fr; gap: 32px; padding: 40px 28px 28px; }
        }

        /* Mobile: below 768px */
        @media (max-width: 768px) {
            main { padding: 20px 14px 60px; }
            .nav-inner { padding: 0 14px; height: 58px; }
            .nav-menu-toggle { display: inline-flex; }

            .nav-links {
                display: none !important;
                position: fixed;
                top: 58px; left: 0; right: 0;
                background: var(--white);
                flex-direction: column;
                align-items: flex-start;
                gap: 0;
                padding: 10px 0 16px;
                box-shadow: 0 14px 36px rgba(22,8,0,0.15);
                z-index: 300;
                margin-left: 0;
                border-bottom: 2px solid rgba(196,144,16,0.15);
            }

            .nav-links.nav-open {
                display: flex !important;
            }

            .nav-link {
                width: 100%;
                padding: 13px 20px;
                font-size: 14px;
                border-radius: 0;
                border-bottom: 1px solid rgba(196,144,16,0.06);
            }

            .nav-link::after { display: none; }
            .nav-link:hover { background: rgba(196,144,16,0.06); color: var(--red); }

            .nav-admin-wrap { width: 100%; }

            /* Hide user name on small screens, show only avatar */
            .nav-user-name { display: none; }
            .nav-user-btn { padding: 4px 8px 4px 4px; }
            .profile-sidebar {
                position: static !important;
                top: auto !important;
            }
        }

        /* Small phones: below 480px */
        @media (max-width: 480px) {
            main { padding: 16px 12px 50px; }
            
            .page-title-bar h2 { font-size: 18px; }
            .page-title-inner { padding: 12px; }
            
            /* Stat cards */
            .stat-card { padding: 16px 12px; }
            .stat-num { font-size: 28px; }
            .stat-lbl { font-size: 8px; letter-spacing: 1px; }
            
            /* Buttons - full width on small screens */
            .btn { width: 100%; justify-content: center; padding: 12px 16px; }
            .btn + .btn { margin-top: 8px; }
            
            /* Forms */
            .form-input { padding: 10px 12px; font-size: 12px; }
            .form-label { font-size: 10px; }
            
            /* Tables */
            .ps-table { min-width: 420px; }
            .ps-table thead th, .ps-table tbody td { padding: 8px 10px; font-size: 11px; }
            
            /* Badges */
            .badge { padding: 3px 8px; font-size: 10px; }
            
            /* Alerts */
            .alert { padding: 12px 14px; font-size: 13px; }
            
            /* Nav */
            .nav-logo-icon { width: 36px; height: 36px; }
            .nav-logo-main { font-size: 16px; }
            .nav-logo-sub { font-size: 7px; }
            .nav-user-btn { padding: 4px 12px 4px 4px; }
            .nav-avatar { width: 28px; height: 28px; font-size: 11px; }
            .nav-user-name { font-size: 12px; }
            
            /* Hero section adjustments */
            .emergency-banner { padding: 8px 12px; font-size: 10px; }
        }

        /* Extra small: below 360px */
        @media (max-width: 360px) {
            main { padding: 14px 10px 45px; }
            .page-title-bar h2 { font-size: 16px; }
            .stat-num { font-size: 24px; }
            .btn { padding: 10px 14px; font-size: 10px; }
        }
    </style>
</head>
<body>
<div class="page-shell">

    @php $em = \DB::table('settings')->where('key','emergency_mode')->value('value'); @endphp

    @if($em === '1')
        <div class="emergency-banner">Emergency Mode Active &mdash; Donations prioritized for disaster-affected communities</div>
    @else
        <div class="top-bar">
            <span class="top-bar-text">Bridging surplus food with communities in need &middot; Philippines</span>
            <span class="top-bar-text">SDG No. 2 &mdash; <span class="top-bar-gold">Zero Hunger</span></span>
        </div>
    @endif

    @include('layouts.navigation')
    <hr class="gold-rule">

    @isset($header)
        <div class="page-title-bar">
            <div class="page-title-inner">
                <h2>{{ $header }}</h2>
                <div class="page-title-line"></div>
            </div>
        </div>
    @endisset

    <main>{{ $slot }}</main>

    <footer class="main-footer">
        <div class="footer-top">
            <div>
                <div class="footer-brand">Pagkainang Sambayanan</div>
                <p class="footer-desc">A web-based food donation platform connecting generous donors with communities in need across the Philippines.</p>
            </div>
            <div>
                <div class="footer-col-title">Platform</div>
                <a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a>
                <a href="{{ route('donations.create') }}" class="footer-link">Post a Donation</a>
                <a href="{{ route('donations.available') }}" class="footer-link">Available Food</a>
                <a href="{{ route('feedback.index') }}" class="footer-link">Feedback Log</a>
            </div>
            <div>
                <div class="footer-col-title">Account</div>
                <a href="{{ route('profile.edit') }}" class="footer-link">Profile Settings</a>
                <a href="{{ route('donations.index') }}" class="footer-link">Donation History</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-copy">&copy; {{ date('Y') }} Pagkainang Sambayanan. All rights reserved.</span>
            <span class="footer-sdg">UN Sustainable Development Goal No. 2 &mdash; Zero Hunger</span>
        </div>
    </footer>

</div>

<div id="toast-container"></div>
<script>
function showToast(type,title,msg,dur){
    dur=dur||4500;
    var svg={
        success:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        error:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        warning:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
    };
    var c=document.getElementById('toast-container');
    var el=document.createElement('div');
    el.className='ps-toast t-'+type;
    el.innerHTML='<div class="t-icon">'+svg[type]+'</div><div class="t-body"><div class="t-title">'+title+'</div>'+(msg?'<div class="t-msg">'+msg+'</div>':'')+'</div><button class="t-close" onclick="killToast(this.parentElement)">&#x2715;</button>';
    c.appendChild(el);
    setTimeout(function(){killToast(el);},dur);
}
function killToast(el){
    if(!el||el.classList.contains('t-out'))return;
    el.classList.add('t-out');
    setTimeout(function(){el&&el.remove();},320);
}
document.addEventListener('DOMContentLoaded',function(){
    @if(session('login_success'))    showToast('success','Welcome back',"{{ addslashes(session('login_success')) }}"); @endif
    @if(session('donation_success')) showToast('success','Donation posted',"{{ addslashes(session('donation_success')) }}"); @endif
    @if(session('success'))          showToast('success','Done',"{{ addslashes(session('success')) }}"); @endif
    @if(session('error'))            showToast('error','Error',"{{ addslashes(session('error')) }}"); @endif

    // Mobile nav toggle
    // Mobile nav toggle
    document.addEventListener('DOMContentLoaded', function(){
        var toggle = document.getElementById('nav-toggle');
        var links  = document.getElementById('nav-links');
        
        if(toggle && links){
            toggle.addEventListener('click', function(e){
                e.stopPropagation();
                toggle.classList.toggle('is-open');
                links.classList.toggle('nav-open');
            });
            document.addEventListener('click', function(e){
                if(!toggle.contains(e.target) && !links.contains(e.target)){
                    toggle.classList.remove('is-open');
                    links.classList.remove('nav-open');
                }
            });
            // Close nav when a link is clicked
            links.querySelectorAll('a').forEach(function(link){
                link.addEventListener('click', function(){
                    toggle.classList.remove('is-open');
                    links.classList.remove('nav-open');
                });
            });
        }
    });
});
</script>
</body>
</html>