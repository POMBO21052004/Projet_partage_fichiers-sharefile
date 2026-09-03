<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\File;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $year = $request->get('year', date('Y'));

        $stats = [
            'my_files_count' => File::where('user_id', $user->id)->count(),
            'shared_with_me_count' => $user->sharedFiles()->count(),
            'storage_used' => File::where('user_id', $user->id)->sum('size'),
        ];

        // --- Données pour le graphique linéaire (Uploads vs Stockage de l'utilisateur) ---
        $monthlyUploads = [];
        $cumulativeStorage = [];
        $runningTotal = File::where('user_id', $user->id)->where('created_at', '<', "$year-01-01")->sum('size');

        for ($m = 1; $m <= 12; $m++) {
            $monthStart = "$year-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01";
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            
            $uploadsCount = File::where('user_id', $user->id)->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthlyUploads[] = $uploadsCount; // Nombre de fichiers
            
            $uploadsSize = File::where('user_id', $user->id)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('size');
            $runningTotal += $uploadsSize;
            $cumulativeStorage[] = round($runningTotal / 1024 / 1024, 2); // Mo
        }

        // --- Données pour le graphique circulaire (Répartition par type) ---
        $totalFiles = $stats['my_files_count'];
        $docCount = File::where('user_id', $user->id)->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx'])->count();
        $imgCount = File::where('user_id', $user->id)->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])->count();
        $zipCount = File::where('user_id', $user->id)->whereIn('extension', ['zip', 'rar', '7z', 'tar', 'gz'])->count();
        $otherCount = $totalFiles - ($docCount + $imgCount + $zipCount);
        
        $distribution = [
            'docs' => $totalFiles > 0 ? round(($docCount / $totalFiles) * 100) : 0,
            'imgs' => $totalFiles > 0 ? round(($imgCount / $totalFiles) * 100) : 0,
            'zips' => $totalFiles > 0 ? round(($zipCount / $totalFiles) * 100) : 0,
            'others' => $totalFiles > 0 ? round(($otherCount / $totalFiles) * 100) : 0,
        ];

        $myRecentFiles = File::where('user_id', $user->id)->latest()->take(5)->get();
        $recentSharedFiles = $user->sharedFiles()->latest()->take(5)->get();
        
        $years = File::where('user_id', $user->id)->selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->sortDesc();
        if ($years->isEmpty()) $years = [date('Y')];

        return view('user.dashboard', compact(
            'stats', 'myRecentFiles', 'recentSharedFiles', 
            'monthlyUploads', 'cumulativeStorage', 'distribution', 
            'year', 'years'
        ));
    }
}
