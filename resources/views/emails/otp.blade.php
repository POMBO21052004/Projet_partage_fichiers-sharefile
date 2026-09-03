<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto; padding: 40px; background-color: #f8fafc; }
        .card { background-color: #ffffff; border-radius: 24px; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .logo { font-size: 24px; font-weight: 900; color: #4f46e5; text-transform: uppercase; margin-bottom: 30px; text-align: center; letter-spacing: -1px; }
        .title { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px; text-align: center; }
        .subtitle { font-size: 14px; color: #64748b; text-align: center; margin-bottom: 30px; }
        .otp-box { background-color: #f1f5f9; border-radius: 16px; padding: 20px; text-align: center; margin: 30px 0; border: 2px dashed #cbd5e1; }
        .otp-code { font-size: 32px; font-weight: 900; color: #4f46e5; letter-spacing: 8px; margin: 0; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 30px; line-height: 1.6; }
        .warning { font-size: 12px; color: #94a3b8; font-style: italic; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">Share<span style="color: #1e293b;">File</span></div>
            <div class="title">Vérification de sécurité</div>
            <div class="subtitle">Pour continuer, veuillez utiliser le code de vérification ci-dessous.</div>
            
            <div class="otp-box">
                <p class="otp-code">{{ $code }}</p>
            </div>
            
            <p style="font-size: 14px; color: #475569; line-height: 1.6; text-align: center;">
                Ce code est valable pendant <strong>10 minutes</strong>. Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.
            </p>
            
            <div class="footer">
                &copy; {{ date('Y') }} Share File. Tous droits réservés.<br>
                Système de partage de fichiers sécurisé et professionnel.
            </div>
        </div>
        <div class="warning">
            Ceci est un message automatique, merci de ne pas y répondre.
        </div>
    </div>
</body>
</html>
