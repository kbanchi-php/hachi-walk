<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\DateTime;

class Walk extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'latitude',
        'longitude',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * get image path of first photo
     */
    public function getImagePathAttribute()
    {
        return 'walks/' . $this->photos[0]->name;
    }

    /**
     * get image url of first photo
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    /**
     * get image path of first photo
     */
    public function getImagePathRandomAttribute()
    {
        return 'walks/' . $this->photos->random()->name;
    }

    /**
     * get image url of first photo
     */
    public function getImageUrlRandomAttribute()
    {
        return Storage::url($this->image_path_random);
    }

    /**
     * get image paths of all photos
     */
    public function getImagePathsAttribute()
    {
        $paths = [];
        foreach ($this->photos as $photo) {
            $paths[] = 'walks/' . $photo->name;
        }
        return $paths;
    }

    /**
     * get image urls of all photos
     */
    public function getImageUrlsAttribute()
    {
        $urls = [];
        foreach ($this->image_paths as $path) {
            $urls[] = Storage::url($path);
        }
        return $urls;
    }

    /**
     * Get Elaped Time from Meal Post time to current time.
     */
    public function getElapsedTimeAttribute()
    {
        // get current time
        $now = new DateTime();
        // get meal post created at time
        $post_time = new DateTime($this->created_at);
        // check elaped time
        if ($post_time->diff(new DateTime("now -1 mins"))->invert) {
            // if meal post time is within 1 minute
            $elaped_time = $post_time->diff($now)->s . '???';
        } elseif ($post_time->diff(new DateTime("now -1 hours"))->invert) {
            // if meal post time is within 1 hour
            $elaped_time = $post_time->diff($now)->i . '???';
        } elseif ($post_time->diff(new DateTime("now -1 days"))->invert) {
            // if meal post time is within 1 day
            $elaped_time = $post_time->diff($now)->h . '??????';
        } elseif ($post_time->diff(new DateTime("now -1 months"))->invert) {
            // if meal post time is within 1 month
            $elaped_time = $post_time->diff($now)->d . '???';
        } elseif ($post_time->diff(new DateTime("now -1 years"))->invert) {
            // if meal post time is within 1 year
            $elaped_time = $post_time->diff($now)->m . '??????';
        } else {
            // if meal post time is over 1 year
            $elaped_time = '1?????????';
        }
        return $elaped_time;
    }
}
