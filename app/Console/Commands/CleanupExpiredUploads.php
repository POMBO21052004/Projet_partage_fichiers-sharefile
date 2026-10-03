<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ResumableUploadService;

class CleanupExpiredUploads extends Command
{
    protected $signature = 'uploads:cleanup';
    protected $description = 'Nettoie les sessions d\'upload résumables expirées et supprime les fichiers temporaires.';

    public function handle(ResumableUploadService $service)
    {
        $this->info('Démarrage du nettoyage des uploads expirés...');
        
        $count = $service->cleanupExpiredUploads();
        
        $this->info("Nettoyage terminé. {$count} session(s) expirée(s) nettoyée(s).");
    }
}
