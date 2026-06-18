<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','color','description','article_count'];

    public function articles() { return $this->hasMany(Article::class); }

    public function refreshCount(): void
    {
        $this->update([
            'article_count' => $this->articles()->where('status','published')->count()
        ]);
    }
}
