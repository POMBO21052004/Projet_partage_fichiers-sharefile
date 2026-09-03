<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification OTP - Share File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>
<body class="bg-slate-950 flex items-center justify-center min-h-screen p-4 font-sans antialiased">
    <div class="w-full max-w-md text-center">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-white tracking-tighter uppercase mb-2">Share<span class="text-indigo-500">File</span></h1>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-[10px] font-black text-indigo-400 uppercase tracking-widest">
                <span class="material-icons-round text-sm">verified_user</span> Sécurité renforcée
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl shadow-2xl backdrop-blur-xl text-left">
            <h2 class="text-xl font-bold text-white mb-2 text-center">Code de vérification</h2>
            <p class="text-slate-400 text-sm mb-8 text-center leading-relaxed">
                Un code a été envoyé à l'adresse <br>
                <span class="text-indigo-400 font-bold">{{ $email }}</span>
            </p>

            <form action="{{ url('/verify-otp') }}" method="POST" class="space-y-6">
                @csrf
                
                @if($errors->any())
                    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-sm font-bold text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <div class="relative group">
                        <input type="text" name="code" maxlength="6" required autofocus
                            class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl py-4 text-center text-3xl font-black tracking-[0.5em] text-white outline-none transition-all placeholder:text-slate-800"
                            placeholder="000000">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-indigo-600/20 transition-all active:scale-95 transform">
                    Vérifier le code
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-4">Vous n'avez rien reçu ?</p>
                <a href="#" class="text-indigo-400 hover:text-indigo-300 text-xs font-black uppercase tracking-widest transition-colors">Renvoyer un code</a>
            </div>
        </div>

        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mt-8 text-slate-500 hover:text-white text-xs font-bold uppercase tracking-widest transition-colors group">
            <span class="material-icons-round text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour à la connexion
        </a>
    </div>
</body>
</html>
