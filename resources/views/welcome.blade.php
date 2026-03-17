<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagkainang - Sambayanan</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --red:#B02818; --red2:#CC3020; --red3:#E04030;
            --gold:#C49010; --gold2:#DCA820; --gold3:#F0C840;
            --dark:#160800; --dark2:#1E0C02; --dark3:#2A1206;
            --brown:#583018; --text:#260C00; --muted:#7A6050;
            --light:#F6EED8; --lighter:#FBF5E8; --white:#FFFDF8;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Jost',sans-serif;background:var(--lighter);color:var(--text);overflow-x:hidden;}
        h1,h2,h3,h4,.serif{font-family:'Cormorant Garamond',serif;}
        a{text-decoration:none;}

        /* ── NAV ── */
        .welcome-nav{
            position:fixed;top:0;left:0;right:0;z-index:100;
            padding:0 60px;height:72px;
            display:flex;align-items:center;justify-content:space-between;
            background:rgba(251,245,232,0.92);backdrop-filter:blur(12px);
            border-bottom:1px solid rgba(196,144,16,0.12);
            transition:all 0.3s;
        }
        .nav-logo-wrap{display:flex;align-items:center;gap:12px;}
        .nav-logo-icon{width:40px;height:40px;border-radius:10px;background:linear-gradient(145deg,var(--red),var(--red2));display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(176,40,24,0.28);}
        .nav-logo-main{font-family:'Cormorant Garamond',serif;font-size:19px;font-weight:700;color:var(--dark);line-height:1;}
        .nav-logo-sub{font-size:8px;font-weight:700;color:var(--gold);letter-spacing:3.5px;text-transform:uppercase;margin-top:3px;}
        .nav-actions{display:flex;align-items:center;gap:12px;}
        .btn-nav-login{padding:9px 22px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;border:1.5px solid var(--red);color:var(--red);transition:all 0.2s;font-family:'Jost',sans-serif;background:transparent;cursor:pointer;}
        .btn-nav-login:hover{background:var(--red);color:white;}
        .btn-nav-reg{padding:9px 22px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;background:var(--red);color:white;box-shadow:0 4px 14px rgba(176,40,24,0.28);transition:all 0.2s;font-family:'Jost',sans-serif;border:none;cursor:pointer;}
        .btn-nav-reg:hover{background:var(--red2);transform:translateY(-1px);}

        /* ── HERO ── */
        .hero{
            min-height:100vh;display:flex;align-items:center;justify-content:center;
            text-align:center;padding:120px 24px 80px;
            position:relative;overflow:hidden;
        }
        .hero-bg{
            position:absolute;inset:0;
            background:radial-gradient(ellipse 80% 60% at 50% 40%,rgba(196,144,16,0.08) 0%,transparent 70%),
                        radial-gradient(ellipse 50% 40% at 20% 80%,rgba(176,40,24,0.06) 0%,transparent 60%),
                        var(--lighter);
        }
        .hero-bg::before{
            content:'';position:absolute;inset:0;
            background-image:linear-gradient(rgba(196,144,16,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(196,144,16,0.04) 1px,transparent 1px);
            background-size:60px 60px;
        }
        .hero-content{position:relative;z-index:1;max-width:760px;width:100%;}
        .hero-badge{
            display:inline-flex;align-items:center;gap:10px;
            background:var(--white);border:1px solid rgba(196,144,16,0.2);
            border-radius:99px;padding:8px 18px;margin-bottom:32px;
            font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);
            box-shadow:0 2px 12px rgba(22,8,0,0.06);
            animation:fadeDown 0.7s ease both;
        }
        .hero-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--gold3);flex-shrink:0;}
        .hero-title{
            font-family:'Cormorant Garamond',serif;
            font-size:clamp(48px,10vw,96px);font-weight:700;
            line-height:0.95;letter-spacing:-2px;
            color:var(--dark);margin-bottom:24px;
            animation:fadeUp 0.7s 0.1s ease both;
        }
        .hero-title-red{color:var(--red);}
        .hero-subtitle{
            font-size:15px;color:var(--muted);line-height:1.8;max-width:480px;
            margin:0 auto 40px;animation:fadeUp 0.7s 0.2s ease both;
        }
        .hero-btns{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;animation:fadeUp 0.7s 0.3s ease both;}
        .hero-btn-primary{
            padding:15px 30px;border-radius:10px;font-size:12px;font-weight:700;
            letter-spacing:0.8px;text-transform:uppercase;
            background:var(--red);color:white;border:none;cursor:pointer;
            box-shadow:0 8px 24px rgba(176,40,24,0.3);
            transition:all 0.25s;font-family:'Jost',sans-serif;
            display:inline-flex;align-items:center;gap:10px;
        }
        .hero-btn-primary:hover{background:var(--red2);transform:translateY(-3px);box-shadow:0 14px 36px rgba(176,40,24,0.4);}
        .hero-btn-secondary{
            padding:15px 30px;border-radius:10px;font-size:12px;font-weight:700;
            letter-spacing:0.8px;text-transform:uppercase;
            background:var(--white);color:var(--brown);border:none;cursor:pointer;
            border:1.5px solid rgba(196,144,16,0.25);
            box-shadow:0 4px 16px rgba(22,8,0,0.06);
            transition:all 0.25s;font-family:'Jost',sans-serif;
            display:inline-flex;align-items:center;gap:10px;
        }
        .hero-btn-secondary:hover{border-color:var(--gold);transform:translateY(-3px);box-shadow:0 10px 28px rgba(22,8,0,0.1);color:var(--text);}
        .hero-footer-note{
            margin-top:40px;display:inline-flex;align-items:center;gap:10px;
            background:var(--white);border:1px solid rgba(196,144,16,0.15);
            border-radius:99px;padding:10px 20px;
            font-size:12px;color:var(--muted);
            box-shadow:0 2px 10px rgba(22,8,0,0.04);
            animation:fadeUp 0.7s 0.4s ease both;
            flex-wrap:wrap;justify-content:center;
        }
        .scroll-indicator{position:absolute;bottom:40px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:8px;animation:fadeUp 1s 0.8s ease both;}
        .scroll-text{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:rgba(122,96,80,0.4);font-weight:600;}
        .scroll-line{width:1px;height:40px;background:linear-gradient(180deg,rgba(196,144,16,0.4),transparent);animation:scrollPulse 2s ease-in-out infinite;}
        @keyframes scrollPulse{0%,100%{opacity:0.3;transform:scaleY(0.8);}50%{opacity:1;transform:scaleY(1);}}

        /* ── STATS BAND ── */
        .stats-band{background:var(--white);border-top:1px solid rgba(196,144,16,0.1);border-bottom:1px solid rgba(196,144,16,0.1);}
        .stats-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);}
        .stat-item{padding:36px 20px;text-align:center;border-right:1px solid rgba(196,144,16,0.1);}
        .stat-item:last-child{border-right:none;}
        .stat-n{font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:700;line-height:1;}
        .stat-l{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:2px;margin-top:6px;font-weight:600;}

        /* ── FEATURES ── */
        .section{padding:80px 40px;max-width:1200px;margin:0 auto;}
        .section-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:16px;}
        .section-eyebrow::before{content:'';width:32px;height:1.5px;background:var(--gold);display:block;}
        .section-h2{font-family:'Cormorant Garamond',serif;font-size:clamp(32px,4vw,52px);font-weight:700;color:var(--dark);line-height:1.05;letter-spacing:-1px;margin-bottom:16px;}
        .section-lead{font-size:14px;color:var(--muted);line-height:1.8;max-width:480px;}

        .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:48px;}
        .feat-card{
            background:var(--white);border-radius:18px;padding:28px 24px;
            border:1px solid rgba(196,144,16,0.1);
            box-shadow:0 2px 8px rgba(22,8,0,0.04),0 8px 28px rgba(22,8,0,0.04);
            transition:all 0.35s ease;position:relative;overflow:hidden;
        }
        .feat-card::before{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--gold),var(--gold3));transform:scaleX(0);transform-origin:left;transition:transform 0.4s ease;}
        .feat-card:hover{transform:translateY(-6px);box-shadow:0 12px 40px rgba(22,8,0,0.1);border-color:rgba(196,144,16,0.2);}
        .feat-card:hover::before{transform:scaleX(1);}
        .feat-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;flex-shrink:0;}
        .feat-title{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--dark);margin-bottom:8px;line-height:1.2;}
        .feat-text{font-size:13px;color:var(--muted);line-height:1.75;}

        /* ── HOW IT WORKS ── */
        .how-section{background:var(--dark);padding:80px 40px;}
        .how-inner{max-width:1100px;margin:0 auto;}
        .how-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:16px;}
        .how-eyebrow::before{content:'';width:32px;height:1.5px;background:var(--gold);display:block;}
        .how-h2{font-family:'Cormorant Garamond',serif;font-size:clamp(32px,4vw,52px);font-weight:700;color:white;line-height:1.05;letter-spacing:-1px;margin-bottom:12px;}
        .how-lead{font-size:14px;color:rgba(255,255,255,0.4);line-height:1.8;}
        .how-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:28px;margin-top:56px;position:relative;}
        .how-steps::before{content:'';position:absolute;top:36px;left:10%;right:10%;height:1px;background:linear-gradient(90deg,transparent,rgba(196,144,16,0.3) 20%,rgba(196,144,16,0.3) 80%,transparent);}
        .how-step{text-align:center;position:relative;z-index:1;}
        .how-step-num{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold2));display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:700;color:var(--dark);margin:0 auto 20px;box-shadow:0 8px 24px rgba(196,144,16,0.35);}
        .how-step-title{font-family:'Cormorant Garamond',serif;font-size:19px;font-weight:700;color:white;margin-bottom:8px;}
        .how-step-text{font-size:13px;color:rgba(255,255,255,0.4);line-height:1.7;}

        /* ── CTA ── */
        .cta-section{padding:80px 24px;text-align:center;}
        .cta-inner{max-width:620px;margin:0 auto;background:var(--white);border-radius:24px;padding:56px 40px;border:1px solid rgba(196,144,16,0.15);box-shadow:0 4px 16px rgba(22,8,0,0.05),0 40px 100px rgba(22,8,0,0.08);position:relative;overflow:hidden;}
        .cta-inner::before{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(196,144,16,0.08),transparent 70%);}
        .cta-inner::after{content:'';position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(176,40,24,0.06),transparent 70%);}
        .cta-icon{font-size:40px;margin-bottom:20px;position:relative;z-index:1;}
        .cta-h2{font-family:'Cormorant Garamond',serif;font-size:clamp(28px,5vw,40px);font-weight:700;color:var(--dark);margin-bottom:14px;position:relative;z-index:1;}
        .cta-text{font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:28px;position:relative;z-index:1;}
        .cta-btns{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;position:relative;z-index:1;}
        .btn-cta-primary{padding:14px 28px;border-radius:10px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;background:var(--red);color:white;border:none;cursor:pointer;box-shadow:0 6px 20px rgba(176,40,24,0.28);transition:all 0.25s;font-family:'Jost',sans-serif;}
        .btn-cta-primary:hover{background:var(--red2);transform:translateY(-2px);box-shadow:0 10px 28px rgba(176,40,24,0.4);}
        .btn-cta-outline{padding:14px 28px;border-radius:10px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;background:transparent;color:var(--red);border:1.5px solid var(--red);cursor:pointer;transition:all 0.25s;font-family:'Jost',sans-serif;}
        .btn-cta-outline:hover{background:var(--red);color:white;transform:translateY(-2px);}

        /* ── FOOTER ── */
        .welcome-footer{background:var(--dark);padding:24px 40px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.05);flex-wrap:wrap;gap:12px;}
        .footer-brand{font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:700;color:var(--gold3);}
        .footer-tagline{font-size:11px;color:rgba(255,255,255,0.25);margin-top:3px;}
        .footer-sdg{font-size:11px;color:rgba(196,144,16,0.5);letter-spacing:0.5px;}

        .gold-rule{height:1px;border:none;background:linear-gradient(90deg,transparent,rgba(196,144,16,0.35) 30%,var(--gold2) 50%,rgba(196,144,16,0.35) 70%,transparent);}

        @keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeDown{from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);}}

        /* ── MODAL ── */
        .modal-overlay{position:fixed;inset:0;z-index:999;background:rgba(22,8,0,0.6);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:opacity 0.3s ease,visibility 0.3s ease;}
        .modal-overlay.active{opacity:1;visibility:visible;}
        .modal-card{width:100%;max-width:440px;background:var(--white);border-radius:20px;border:1px solid rgba(196,144,16,0.15);box-shadow:0 4px 16px rgba(22,8,0,0.1),0 40px 100px rgba(22,8,0,0.3);overflow:hidden;transform:translateY(28px) scale(0.97);transition:transform 0.35s cubic-bezier(0.175,0.885,0.32,1.275);}
        .modal-overlay.active .modal-card{transform:translateY(0) scale(1);}
        .modal-header{background:linear-gradient(145deg,var(--red),var(--red2) 60%,var(--red3));padding:32px 36px 24px;text-align:center;position:relative;overflow:hidden;}
        .modal-header::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.06);}
        .modal-header::after{content:'';position:absolute;bottom:-30px;left:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.04);}
        .modal-header-line{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold) 30%,var(--gold3) 50%,var(--gold) 70%,transparent);}
        .modal-close{position:absolute;top:14px;right:14px;z-index:10;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;}
        .modal-close:hover{background:rgba(255,255,255,0.28);}
        .modal-logo{width:48px;height:48px;border-radius:13px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border:1px solid rgba(255,255,255,0.15);position:relative;z-index:1;}
        .modal-title{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:white;margin-bottom:4px;position:relative;z-index:1;}
        .modal-subtitle{font-size:12px;color:rgba(255,255,255,0.6);position:relative;z-index:1;}
        .modal-body{padding:24px 32px 20px;}
        .modal-footer-links{padding:14px 32px 22px;text-align:center;border-top:1px solid rgba(196,144,16,0.1);}
        .m-form-group{margin-bottom:14px;}
        .m-form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
        .m-form-label{display:block;font-size:10px;font-weight:700;color:var(--muted);margin-bottom:5px;letter-spacing:1.5px;text-transform:uppercase;}
        .m-form-input{width:100%;padding:10px 13px;border-radius:10px;border:1.5px solid rgba(196,144,16,0.18);font-size:13px;font-family:'Jost',sans-serif;background:var(--lighter);color:var(--text);transition:all 0.2s;outline:none;}
        .m-form-input:focus{border-color:var(--red);background:var(--white);box-shadow:0 0 0 3px rgba(176,40,24,0.08);}
        .m-form-input::placeholder{color:rgba(122,96,80,0.38);}
        .btn-modal{width:100%;padding:12px;border-radius:10px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;background:var(--red);color:white;border:none;cursor:pointer;box-shadow:0 6px 20px rgba(176,40,24,0.28);transition:all 0.25s;font-family:'Jost',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;}
        .btn-modal:hover{background:var(--red2);transform:translateY(-2px);box-shadow:0 10px 28px rgba(176,40,24,0.4);}
        .m-link{color:var(--red);font-weight:600;transition:color 0.2s;cursor:pointer;}
        .m-link:hover{color:var(--red2);}
        .m-check-wrap{display:flex;align-items:center;gap:10px;}
        .m-check-wrap input[type=checkbox]{width:16px;height:16px;accent-color:var(--red);cursor:pointer;}
        .m-check-label{font-size:13px;color:var(--muted);}
        .m-error-box{background:#FEE;border:1px solid rgba(176,40,24,0.2);border-radius:10px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:var(--red);}

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 768px) {
            /* Nav */
            .welcome-nav { padding: 0 20px; height: 60px; }
            .nav-logo-main { font-size: 16px; }
            .btn-nav-login { padding: 7px 14px; font-size: 11px; }
            .btn-nav-reg   { padding: 7px 14px; font-size: 11px; }

            /* Hero */
            .hero { padding: 100px 20px 80px; }
            .hero-badge { font-size: 9px; padding: 7px 14px; letter-spacing: 1.5px; }
            .hero-subtitle { font-size: 14px; }
            .hero-btns { flex-direction: column; align-items: stretch; gap: 10px; }
            .hero-btn-primary, .hero-btn-secondary { justify-content: center; width: 100%; }
            .hero-footer-note { font-size: 11px; padding: 8px 16px; gap: 6px; }
            .scroll-indicator { display: none; }

            /* Stats: 2x2 grid */
            .stats-inner { grid-template-columns: repeat(2, 1fr); }
            .stat-item { border-right: none; border-bottom: 1px solid rgba(196,144,16,0.1); padding: 24px 16px; }
            .stat-item:nth-child(1), .stat-item:nth-child(2) { border-right: 1px solid rgba(196,144,16,0.1); }
            .stat-item:nth-child(3), .stat-item:nth-child(4) { border-bottom: none; }
            .stat-n { font-size: 36px; }

            /* Features: single column */
            .section { padding: 56px 20px; }
            .features-grid { grid-template-columns: 1fr; gap: 14px; margin-top: 32px; }
            .feat-card { padding: 22px 20px; border-radius: 14px; }
            .feat-title { font-size: 18px; }

            /* How it works: 2x2 */
            .how-section { padding: 56px 20px; }
            .how-steps { grid-template-columns: repeat(2, 1fr); gap: 32px; }
            .how-steps::before { display: none; }
            .how-step-num { width: 60px; height: 60px; font-size: 24px; }

            /* CTA */
            .cta-section { padding: 56px 16px; }
            .cta-inner { padding: 40px 24px; border-radius: 18px; }
            .cta-btns { flex-direction: column; align-items: stretch; }
            .btn-cta-primary, .btn-cta-outline { width: 100%; }

            /* Footer */
            .welcome-footer { padding: 20px; flex-direction: column; align-items: flex-start; }

            /* Modal */
            .modal-card { max-width: 100%; border-radius: 16px; }
            .modal-header { padding: 24px 24px 20px; }
            .modal-body { padding: 20px 24px 16px; }
            .modal-footer-links { padding: 12px 24px 20px; }
            .m-form-row { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .hero-title { letter-spacing: -1px; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); }
            .stat-n { font-size: 32px; }
            .stat-l { font-size: 9px; letter-spacing: 1px; }
            .section-h2 { font-size: 28px; }
            .how-steps { grid-template-columns: 1fr; }
            .how-steps::before { display: none; }
        }
    </style>
</head>
<body>

{{-- LOGIN MODAL --}}
<div class="modal-overlay" id="loginModal">
    <div class="modal-card">
        <div class="modal-header">
            <button class="modal-close" onclick="closeModal('loginModal')">
                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="modal-logo">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" style="width:34px;height:34px;border-radius:10px;object-fit:cover;">
            </div>
            <div class="modal-title">Welcome Back!</div>
            <div class="modal-subtitle">Log in to Pagkainang Sambayanan</div>
            <div class="modal-header-line"></div>
        </div>
        <div class="modal-body">
            @if ($errors->any() && old('_form') === 'login')
                <div class="m-error-box">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="_form" value="login">
                <div class="m-form-group">
                    <label class="m-form-label">Email Address</label>
                    <input type="email" name="email" class="m-form-input" placeholder="e.g., juan@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Password</label>
                    <input type="password" name="password" class="m-form-input" placeholder="Enter your password" required>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:8px;flex-wrap:wrap;">
                    <div class="m-check-wrap">
                        <input type="checkbox" name="remember" id="remember_me">
                        <label for="remember_me" class="m-check-label">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="m-link" style="font-size:12px;">Forgot password?</a>
                    @endif
                </div>
                <button type="submit" class="btn-modal">
                    Log In
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>

                <div style="text-align: center; margin: 20px 0; color: #666; font-size: 13px; position: relative;">
                    <span style="background: white; padding: 0 10px; position: relative; z-index: 1;">OR</span>
                    <div style="border-bottom: 1px solid #eee; position: absolute; top: 50%; width: 100%; z-index: 0;"></div>
                </div>

                <a href="{{ url('auth/google') }}" class="btn-auth" style="background: white; border: 1px solid #ddd; color: #444; display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; padding: 12px; border-radius: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </a>
            </form>
        </div>
        <div class="modal-footer-links">
            <span style="font-size:13px;color:var(--muted);">Don't have an account?</span>
            <span style="margin-left:6px;" class="m-link" onclick="switchModal('loginModal','registerModal')">Create one here</span>
        </div>
    </div>
</div>

{{-- REGISTER MODAL --}}
<div class="modal-overlay" id="registerModal">
    <div class="modal-card">
        <div class="modal-header">
            <button class="modal-close" onclick="closeModal('registerModal')">
                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="modal-logo">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" style="width:34px;height:34px;border-radius:10px;object-fit:cover;">
            </div>
            <div class="modal-title">Create Account</div>
            <div class="modal-subtitle">Join Pagkainang Sambayanan today</div>
            <div class="modal-header-line"></div>
        </div>
        <div class="modal-body">
            @if ($errors->any() && old('_form') === 'register')
                <div class="m-error-box">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            @endif
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="_form" value="register">
                <div class="m-form-group">
                    <label class="m-form-label">Full Name</label>
                    <input type="text" name="name" class="m-form-input" placeholder="e.g., Juan dela Cruz" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Email Address</label>
                    <input type="email" name="email" class="m-form-input" placeholder="e.g., juan@email.com" value="{{ old('email') }}" required>
                </div>
                <div class="m-form-row m-form-group">
                    <div>
                        <label class="m-form-label">Password</label>
                        <input type="password" name="password" class="m-form-input" placeholder="Min. 8 chars" required>
                    </div>
                    <div>
                        <label class="m-form-label">Confirm</label>
                        <input type="password" name="password_confirmation" class="m-form-input" placeholder="Re-enter" required>
                    </div>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Register As</label>
                    <select name="role" class="m-form-input" required>
                        <option value="" disabled selected>Choose your role</option>
                        <option value="donor" {{ old('role') === 'donor' ? 'selected' : '' }}>Donor (Restaurant / Individual)</option>
                        <option value="charity" {{ old('role') === 'charity' ? 'selected' : '' }}>Charity / NGO</option>
                    </select>
                </div>
                <button type="submit" class="btn-modal">
                    Create Account
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
                <div style="position: relative; text-align: center; margin: 20px 0;">
                    <div style="border-top: 1px solid #eee; position: absolute; width: 100%; top: 50%;"></div>
                    <span style="background: white; padding: 0 15px; color: #999; font-size: 12px; position: relative;">OR REGISTER WITH</span>
                </div>

                <a id="google-reg-btn" href="#" onclick="handleGoogleRegister(event)" class="btn-auth"
                   style="background: white; border: 1px solid #ddd; color: #444; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 10px; text-decoration: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Continue with Google
                </a>
            </form>
        </div>
        <div class="modal-footer-links">
            <span style="font-size:13px;color:var(--muted);">Already have an account?</span>
            <span style="margin-left:6px;" class="m-link" onclick="switchModal('registerModal','loginModal')">Log in here</span>
        </div>
    </div>
</div>

{{-- NAV --}}
<nav class="welcome-nav">
    <div class="nav-logo-wrap">
        <div class="nav-logo-icon" style="background:transparent;box-shadow:none;border-radius:14px;padding:0;">
            <img src="{{ asset('logo.jpg') }}" alt="Pagkainang Sambayanan" style="display:block;width:36px;height:36px;border-radius:12px;object-fit:cover;">
        </div>
        <div>
            <div class="nav-logo-main">Pagkainang -</div>
            <div class="nav-logo-sub">Sambayanan</div>
        </div>
    </div>
    <div class="nav-actions">
        <button onclick="openModal('loginModal')" class="btn-nav-login">Log In</button>
        <button onclick="openModal('registerModal')" class="btn-nav-reg">Register</button>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Pagkain para sa sambayanan
        </div>
        <h1 class="hero-title">
            Pagkainang<br>
            <span class="hero-title-red">Sambayanan</span>
        </h1>
        <p class="hero-subtitle">A web-based food donation platform bridging surplus food from restaurants and donors to communities in need across the Philippines.</p>
        <div class="hero-btns">
            <button onclick="openModal('registerModal')" class="hero-btn-primary">
                Start Donating
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
            <button onclick="openModal('loginModal')" class="hero-btn-secondary">
                Browse Donations
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
        </div>
        <div class="hero-footer-note">
            <span style="font-size:16px;line-height:1;">🇵🇭</span>
            <span style="font-weight:700;color:var(--text);">Made for Filipino communities</span>
            <span style="color:rgba(122,96,80,0.4);">·</span>
            <span>Fighting hunger one meal at a time</span>
        </div>
    </div>
    <div class="scroll-indicator">
        <span class="scroll-text">Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

<hr class="gold-rule">

{{-- STATS --}}
@php
    $totalDonations = \App\Models\Donation::withTrashed()->count();
    $completed = \App\Models\Donation::withTrashed()->whereNotNull('claimed_by')->count();
    $charities = \App\Models\User::where('role','charity')->where('verification_status','approved')->count();
    $donors = \App\Models\User::where('role','donor')->count();
@endphp
<div class="stats-band">
    <div class="stats-inner">
        <div class="stat-item"><div class="stat-n" style="color:var(--red);">{{ $totalDonations }}</div><div class="stat-l">Total Donations</div></div>
        <div class="stat-item"><div class="stat-n" style="color:#1A7A40;">{{ $completed }}</div><div class="stat-l">Completed</div></div>
        <div class="stat-item"><div class="stat-n" style="color:var(--gold);">{{ $charities }}</div><div class="stat-l">Verified Charities</div></div>
        <div class="stat-item"><div class="stat-n" style="color:#1A5AA8;">{{ $donors }}</div><div class="stat-l">Donors</div></div>
    </div>
</div>

{{-- FEATURES --}}
<div class="section">
    <div class="section-eyebrow">Why Us</div>
    <h2 class="section-h2">Why Pagkainang Sambayanan?</h2>
    <p class="section-lead">Everything you need to fight hunger in your community — simple, transparent, and impactful.</p>

    <div class="features-grid">
        @foreach([
            ['icon'=>'<svg width="22" height="22" fill="none" stroke="var(--red)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>','bg'=>'rgba(176,40,24,0.08)','title'=>'Easy Food Posting','text'=>'Restaurants, cafes, and individuals can post surplus food in seconds with target audience tags like Seniors, Children, and Families.'],
            ['icon'=>'<svg width="22" height="22" fill="none" stroke="#1A7A40" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>','bg'=>'rgba(26,122,64,0.08)','title'=>'Verified Charities','text'=>'All charity organizations go through admin verification before they can claim donations — ensuring food reaches legitimate beneficiaries.'],
            ['icon'=>'<svg width="22" height="22" fill="none" stroke="var(--red)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>','bg'=>'rgba(176,40,24,0.08)','title'=>'Emergency Mode','text'=>'During disasters, admins can activate Emergency Mode to prioritize food donations for affected communities across the Philippines.'],
            ['icon'=>'<svg width="22" height="22" fill="none" stroke="#1A5AA8" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>','bg'=>'rgba(26,90,168,0.08)','title'=>'Transparency & Feedback','text'=>'Charities upload photos as proof of food distribution, creating a transparent log that donors and the public can view anytime.'],
            ['icon'=>'<svg width="22" height="22" fill="none" stroke="var(--gold)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>','bg'=>'rgba(196,144,16,0.08)','title'=>'Donation History','text'=>'Full tracking of all donations — from posting to claiming to completion — gives everyone visibility into the platform\'s impact.'],
            ['icon'=>'<svg width="22" height="22" fill="none" stroke="#1A7A40" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>','bg'=>'rgba(26,122,64,0.08)','title'=>'Zero Hunger Mission','text'=>'Aligned with the United Nations Sustainable Development Goal No. 2 — working toward a Philippines where no one goes to bed hungry.'],
        ] as $f)
        <div class="feat-card">
            <div class="feat-icon" style="background:{{ $f['bg'] }};">{!! $f['icon'] !!}</div>
            <div class="feat-title">{{ $f['title'] }}</div>
            <p class="feat-text">{{ $f['text'] }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- HOW IT WORKS --}}
<div class="how-section">
    <div class="how-inner">
        <div class="how-eyebrow">Process</div>
        <h2 class="how-h2">How It Works</h2>
        <p class="how-lead">Simple steps to make a difference in your community.</p>
        <div class="how-steps">
            @foreach([
                ['1','Register','Sign up as a Donor or Charity to get started on the platform.'],
                ['2','Post or Browse','Donors post surplus food. Charities browse and claim available donations.'],
                ['3','Claim & Distribute','Verified charities claim donations and distribute food to beneficiaries.'],
                ['4','Submit Proof','Charities upload photos as proof of distribution for full transparency.'],
            ] as [$n,$t,$d])
            <div class="how-step">
                <div class="how-step-num">{{ $n }}</div>
                <div class="how-step-title">{{ $t }}</div>
                <p class="how-step-text">{{ $d }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="cta-section">
    <div class="cta-inner">
        <div class="cta-icon">🌾</div>
        <h2 class="cta-h2">Ready to Make a Difference?</h2>
        <p class="cta-text">Join donors and charities already fighting hunger across the Philippines. Every meal shared is a life changed.</p>
        <div class="cta-btns">
            <button onclick="openModal('registerModal')" class="btn-cta-primary">Get Started — It's Free</button>
            <button onclick="openModal('loginModal')" class="btn-cta-outline">Already have an account?</button>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer class="welcome-footer">
    <div>
        <div class="footer-brand">Pagkainang Sambayanan</div>
        <div class="footer-tagline">Bridging surplus food with communities in need · Philippines</div>
    </div>
    <div class="footer-sdg">⚡ Supporting UN SDG No. 2: Zero Hunger</div>
</footer>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }
    function switchModal(closeId, openId) {
        closeModal(closeId);
        setTimeout(() => openModal(openId), 200);
    }
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape')
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
    });
    @if($errors->any() && old('_form') === 'login')
        openModal('loginModal');
    @elseif($errors->any() && old('_form') === 'register')
        openModal('registerModal');
    @endif

    function handleGoogleRegister(e) {
        e.preventDefault();
        const role = document.querySelector('#registerModal select[name="role"]').value;
        if (!role) {
            alert('Please select a role (Donor or Charity) before continuing with Google.');
            return;
        }
        window.location.href = '{{ url("auth/google") }}?role=' + role;
    }
</script>
</body>
</html>