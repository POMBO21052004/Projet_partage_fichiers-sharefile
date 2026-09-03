<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié - Share File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>
<body class="bg-slate-950 flex items-center justify-center min-h-screen p-4 font-sans antialiased">
    <div class="w-full max-w-md text-center">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-white tracking-tighter uppercase mb-2">Share<span class="text-indigo-500">File</span></h1>
            <p class="text-slate-400 text-xs font-black uppercase tracking-widest">Récupération de compte</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl shadow-2xl backdrop-blur-xl text-left">
            <h2 class="text-xl font-bold text-white mb-2 text-center">Mot de passe oublié ?</h2>
            <p class="text-slate-400 text-sm mb-8 text-center leading-relaxed">
                Entrez votre adresse email pour recevoir un code de vérification.
            </p>

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                
                @if($errors->any())
                    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-sm font-bold text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2 ml-1">Adresse Email</label>
                    <div class="relative group">
                        <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-500 transition-colors">alternate_email</span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl py-3.5 pl-12 text-white font-bold outline-none transition-all placeholder:text-slate-700"
                            placeholder="votre@email.com">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-indigo-600/20 transition-all active:scale-95 transform">
                    Envoyer le code
                </button>
            </form>
        </div>

        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mt-8 text-slate-500 hover:text-white text-xs font-bold uppercase tracking-widest transition-colors group">
            <span class="material-icons-round text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour à la connexion
        </a>
    </div>
</body>
</html>
