<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Task
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $status
 *
 * @property User $user
 *
 */
class Task extends Model
{
    use HasFactory, HasUuids;

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'title', 'description', 'status'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
