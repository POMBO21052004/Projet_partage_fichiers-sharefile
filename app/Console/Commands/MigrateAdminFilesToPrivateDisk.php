<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateAdminFilesToPrivateDisk extends Command
{
    protected $signature = 'files:migrate-to-private
                            {--dry-run : Simule la migration sans effectuer de changements}
                            {--force : Execute sans confirmation}';

    protected $description = 'Migre les fichiers admin du disque public vers le disque private, et met a jour les chemins en base de donnees.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('[MODE SIMULATION] Aucune modification ne sera effectuee.');
        }

        // Fichiers dont le chemin commence par "uploads/" = ancien comportement admin (disque public)
        $files = File::where('path', 'like', 'uploads/%')->get();

        if ($files->isEmpty()) {
            $this->info('Aucun fichier a migrer.');
            return 0;
        }

        $this->info("Fichiers a migrer : {$files->count()}");
        $this->table(
            ['ID', 'Nom', 'Ancien chemin', 'Disque source'],
            $files->map(function ($f) {
                $onPublic  = Storage::disk('public')->exists($f->path);
                $onPrivate = Storage::disk('private')->exists($f->path);
                $disk = $onPublic ? 'public' : ($onPrivate ? 'private' : 'INTROUVABLE');
                return [$f->id, $f->name, $f->path, $disk];
            })
        );

        if (!$dryRun && !$this->option('force')) {
            $count = $files->count();
            if (!$this->confirm("Confirmer la migration de {$count} fichier(s) ?")) {
                $this->info('Migration annulee.');
                return 0;
            }
        }

        $migrated = 0;
        $skipped  = 0;
        $errors   = 0;
        $bar      = $this->output->createProgressBar($files->count());
        $bar->start();

        foreach ($files as $file) {
            $bar->advance();

            if (!Storage::disk('public')->exists($file->path)) {
                $this->newLine();
                $this->warn("  [SKIP] ID {$file->id} - introuvable sur public : {$file->path}");
                $skipped++;
                continue;
            }

            $filename = basename($file->path);
            $newPath  = 'files/' . $file->user_id . '/' . now()->format('Y/m') . '/' . $filename;

            if (Storage::disk('private')->exists($newPath)) {
                $this->newLine();
                $this->warn("  [SKIP] ID {$file->id} - deja present sur private : {$newPath}");
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->newLine();
                $this->line("  [DRY-RUN] ID {$file->id} : {$file->path}  =>  {$newPath}");
                $migrated++;
                continue;
            }

            try {
                $content = Storage::disk('public')->get($file->path);
                Storage::disk('private')->put($newPath, $content);
                $file->update(['path' => $newPath]);
                Storage::disk('public')->delete($file->path);
                $migrated++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  [ERREUR] ID {$file->id} - {$e->getMessage()}");
                $errors++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Resultat', 'Nombre'], [
            ['Migres avec succes',                  $migrated],
            ['Ignores (introuvables/deja migres)',  $skipped],
            ['Erreurs',                             $errors],
        ]);

        if ($errors > 0) {
            $this->error('Migration terminee avec des erreurs.');
            return 1;
        }

        $this->info('Migration terminee avec succes.');
        return 0;
    }
}