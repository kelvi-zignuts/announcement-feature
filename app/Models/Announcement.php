<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['message', 'date', 'time','status'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    protected static function booted()
    {
        // parent::boot();

        // Before creating a new role, set the created_by field
        static::creating(function ($model) {
            // dd(Auth::user());
            $user = Auth::user(); // Get the authenticated user
            // dd($user);
            if ($user) {
                $model->created_by = $user->id;
            }
        });

        // Before updating an existing role, set the updated_by field
        static::updating(function ($model) {
            $user = Auth::user(); // Get the authenticated user
            if ($user) {
                $model->updated_by = $user->id;
            }
        });
    }
}
