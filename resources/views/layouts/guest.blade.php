<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pagkainang Sambayanan') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --red:#B02818;--red2:#CC3020;--red3:#E04030;
            --gold:#C49010;--gold2:#DCA820;--gold3:#F0C840;
            --dark:#160800;--dark2:#1E0C02;--dark3:#2A1206;
            --brown:#583018;--text:#260C00;--muted:#7A6050;
            --light:#F6EED8;--lighter:#FBF5E8;--white:#FFFDF8;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}

        body{
            font-family:'Jost',sans-serif;
            min-height:100vh;
            display:flex;
            flex-direction:column;
            background:var(--lighter);
            background-image:
                radial-gradient(ellipse 70% 50% at 20% 20%,rgba(196,144,16,0.07) 0%,transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%,rgba(176,40,24,0.05) 0%,transparent 60%);
            color:var(--text);
        }

        .page-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 20px 40px;
        }

        h1,h2,h3,.serif{font-family:'Cormorant Garamond',serif;}
        a{text-decoration:none;}

        .auth-card{
            width:100%;max-width:460px;
            background:var(--white);border-radius:20px;
            border:1px solid rgba(196,144,16,0.15);
            box-shadow:0 4px 16px rgba(22,8,0,0.06),0 40px 100px rgba(22,8,0,0.12);
            overflow:hidden;
            animation:cardIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275) both;
        }
        @keyframes cardIn{from{opacity:0;transform:translateY(24px) scale(0.97);}to{opacity:1;transform:translateY(0) scale(1);}}

        .auth-header{
            background:linear-gradient(145deg,var(--red),var(--red2) 60%,var(--red3));
            padding:40px 40px 32px;text-align:center;position:relative;overflow:hidden;
        }
        .auth-header::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.06);}
        .auth-header::after{content:'';position:absolute;bottom:-30px;left:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.04);}
        .auth-header-line{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold) 30%,var(--gold3) 50%,var(--gold) 70%,transparent);}
        .auth-logo{width:60px;height:60px;border-radius:16px;background:rgba(255,255,255,0.12);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;border:1px solid rgba(255,255,255,0.15);position:relative;z-index:1;}
        .auth-title{font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:700;color:white;margin-bottom:6px;position:relative;z-index:1;}
        .auth-subtitle{font-size:13px;color:rgba(255,255,255,0.6);position:relative;z-index:1;}

        .auth-body{padding:36px 40px 32px;}
        .auth-footer-links{padding:20px 40px 28px;text-align:center;border-top:1px solid rgba(196,144,16,0.1);}

        .form-group{margin-bottom:20px;}
        .form-label{display:block;font-size:11px;font-weight:700;color:var(--muted);margin-bottom:8px;letter-spacing:1.5px;text-transform:uppercase;}
        .form-input{width:100%;padding:13px 16px;border-radius:10px;border:1.5px solid rgba(196,144,16,0.18);font-size:14px;font-family:'Jost',sans-serif;background:var(--lighter);color:var(--text);transition:all 0.2s;outline:none;}
        .form-input:focus{border-color:var(--red);background:var(--white);box-shadow:0 0 0 4px rgba(176,40,24,0.08);}
        .form-input::placeholder{color:rgba(122,96,80,0.38);}
        .form-error{font-size:12px;color:var(--red);margin-top:6px;font-weight:500;}

        .btn-auth{width:100%;padding:15px;border-radius:10px;font-size:13px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;background:var(--red);color:white;border:none;cursor:pointer;box-shadow:0 6px 20px rgba(176,40,24,0.28);transition:all 0.25s;font-family:'Jost',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;}
        .btn-auth:hover{background:var(--red2);transform:translateY(-2px);box-shadow:0 10px 28px rgba(176,40,24,0.4);}

        .link-gold{color:var(--red);font-weight:600;transition:color 0.2s;}
        .link-gold:hover{color:var(--red2);}
        .link-muted{font-size:13px;color:rgba(122,96,80,0.5);transition:color 0.2s;}
        .link-muted:hover{color:var(--muted);}

        .check-wrap{display:flex;align-items:center;gap:10px;}
        .check-wrap input[type=checkbox]{width:16px;height:16px;accent-color:var(--red);cursor:pointer;}
        .check-label{font-size:13px;color:var(--muted);}

        .admin-hint{background:linear-gradient(135deg,rgba(196,144,16,0.08),rgba(196,144,16,0.04));border:1px solid rgba(196,144,16,0.2);border-radius:10px;padding:14px 16px;margin-bottom:20px;}
        .admin-hint-label{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gold);margin-bottom:8px;}
        .admin-hint-row{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted);margin-top:4px;}
        .admin-hint-key{font-size:11px;font-weight:700;color:var(--text);background:var(--white);border:1px solid rgba(196,144,16,0.2);border-radius:5px;padding:2px 8px;font-family:monospace;}

        .info-box{background:rgba(196,144,16,0.06);border:1px solid rgba(196,144,16,0.15);border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:var(--muted);line-height:1.6;}
        .alert-success{background:#E4F5EB;border:1px solid rgba(26,122,64,0.2);border-radius:10px;padding:12px 16px;font-size:13px;color:#186634;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
    </style>
</head>
<body>

    <div class="page-content">
        {{ $slot }}
    </div>

    <footer style="background:var(--dark);padding:20px 40px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.05);">
        <div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:15px;font-weight:700;color:var(--gold3);">Pagkainang Sambayanan</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.25);margin-top:2px;">Bridging surplus food with communities in need · Philippines</div>
        </div>
        <div style="font-size:11px;color:rgba(196,144,16,0.5);letter-spacing:0.5px;display:flex;align-items:center;gap:6px;">
            <svg width="11" height="11" fill="none" stroke="rgba(196,144,16,0.5)" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Supporting UN SDG No. 2: Zero Hunger
        </div>
    </footer>

</body>
</html>