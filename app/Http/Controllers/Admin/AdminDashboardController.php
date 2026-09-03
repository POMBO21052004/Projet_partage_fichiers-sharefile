<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\File;
use App\Models\Folder;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $totalFiles = File::count();
        $totalFolders = Folder::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalSizeBytes = File::sum('size');
        $totalSize = $this->formatBytes($totalSizeBytes);

        // --- Données pour le graphique linéaire (Uploads vs Stockage) ---
        $monthlyUploads = [];
        $cumulativeStorage = [];
        $runningTotal = File::where('created_at', '<', "$year-01-01")->sum('size');

        for ($m = 1; $m <= 12; $m++) {
            $monthStart = "$year-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01";
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            
            $uploadsCount = File::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthlyUploads[] = $uploadsCount; // Nombre de fichiers
            
            $uploadsSize = File::whereBetween('created_at', [$monthStart, $monthEnd])->sum('size');
            $runningTotal += $uploadsSize;
            $cumulativeStorage[] = round($runningTotal / 1024 / 1024, 2); // Mo
        }

        // --- Données pour le graphique circulaire (Répartition) ---
        $docCount = File::whereIn('extension', ['pdf', 'doc', 'docx', 'txt'])->count();
        $imgCount = File::whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'svg'])->count();
        $otherCount = $totalFiles - ($docCount + $imgCount);
        
        $distribution = [
            'docs' => $totalFiles > 0 ? round(($docCount / $totalFiles) * 100) : 0,
            'imgs' => $totalFiles > 0 ? round(($imgCount / $totalFiles) * 100) : 0,
            'others' => $totalFiles > 0 ? round(($otherCount / $totalFiles) * 100) : 0,
        ];

        $recentFiles = File::with('user')->latest()->take(5)->get();
        $years = File::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->sortDesc();
        if ($years->isEmpty()) $years = [date('Y')];

        return view('admin.dashboard', compact(
            'totalFiles', 'totalFolders', 'totalUsers', 'totalSize', 
            'recentFiles', 'monthlyUploads', 'cumulativeStorage', 
            'distribution', 'year', 'years'
        ));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
