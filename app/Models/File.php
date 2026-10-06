<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'extension',
        'size',
        'folder_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'permissions')
                    ->withPivot('can_download')
                    ->withTimestamps();
    }

    public function getFolderPathAttribute()
    {
        if (!$this->folder_id) {
            return 'Racine';
        }

        $path = [];
        $current = $this->folder;
        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }

        return 'Racine > ' . implode(' > ', $path);
    }
}
