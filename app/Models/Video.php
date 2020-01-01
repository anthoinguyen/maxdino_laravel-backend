<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Schema;

class Video extends BaseModel
{
    protected $table = 'videos';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'link', 'image', 'priority',
    ];
    public static $rules = array(
        'Rule_Create_Video' => [
            'videoTitle' => 'required|string|max:70',
            'videoLink' => 'required|string|max:500',
            'videoImage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'videoPriority' => 'required|numeric|between:1,10',
        ],
        'Rule_Get_Video' => [
            'videoId' => 'required|integer',
        ],
        'Rule_Delete_Video' => [
            'videoId' => 'required|integer',
        ],
        'Rule_Update_Video' => [
            'videoId' => 'required|integer',
            'videoTitle' => 'string|max:70',
            'videoLink' => 'string|max:500',
            'videoImage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'videoPriority' => 'numeric|between:1,10',
        ],
        'Rule_Get_All_Video' => [
            'offset' => 'integer',
            'limit' => 'integer',
            'fieldSort' => 'string',
            'typeSort' => 'in:asc,desc',
            'fieldSearch' => 'string',
            'keySearch' => 'string',
        ],
    );

    public function scopeGetDetail($query)
    {
        return $query->join('users', 'videos.user_id', '=', 'users.id')
        ->select(['videos.*', 'users.username', 'users.avatar']);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function createVideo($userId, $input = array())
    {
        $video = Video::create([
            'user_id' => $userId,
            'title' => $input['videoTitle'],
            'link' => $input['videoLink'],
            'image' => $input['linkImage'],
            'priority' => $input['videoPriority'],
        ]);
        $user = $video->user()->first();
        $video->setAttribute('username', $user->username);
        $video->setAttribute('avatar', $user->avatar);
        return $video;
    }

    public function updateVideo($input)
    {
        if (isset($input['videoTitle'])) {
            $this->title = $input['videoTitle'];
        }
        if (isset($input['videoLink'])) {
            $this->link = $input['videoLink'];
        }
        if (isset($input['videoPriority'])) {
            $this->priority = $input['videoPriority'];
        }
        if (isset($input['linkImage'])) {
            $this->image = $input['linkImage'];
        }
        $this->save();
        $user = $this->user()->first();
        $this->setAttribute('username', $user->username);
        $this->setAttribute('avatar', $user->avatar);
    }

    public static function getAllVideo($input)
    {
        $field = [
            'id' => 'videos.id',
            'username' => 'users.username',
            'title' => 'videos.title',
            'link' => 'videos.link',
            'priority' => 'videos.priority',
            'created_at' => 'videos.created_at',
            'updated_at' => 'videos.updated_at'
        ];
        $listVideos = Video::GetDetail();

        if (isset($input['fieldSort']) && isset($input['typeSort'])) {
            if(!array_key_exists($input['fieldSort'], $field))
            {
                return [
                    'error' => true,
                    'data' => [],
                    'errorCode' => 'fieldSort_not_found'
                ];
            }
            $listVideos->orderBy($field[$input['fieldSort']], $input['typeSort']);
            if ($input['fieldSort'] != 'id')
            {
                $listVideos->orderBy('id', 'desc');
            }
        }

        if (isset($input['fieldSearch']) && isset($input['keySearch'])) {
            if(!array_key_exists($input['fieldSearch'], $field))
            {
                return [
                    'error' => true,
                    'data' => [],
                    'errorCode' => 'fieldSearch_not_found'
                ];
            }
            $listVideos->where($field[$input['fieldSearch']],'like', '%' . $input['keySearch'] . '%');
        }

        $numberOfVideos = $listVideos->count();
        $numberOfPages = 1;
        if (isset($input['limit'])) {
            $listVideos->limit($input['limit']);
            if (isset($input['offset'])) {
                $listVideos->offset($input['offset']);
            }
            $numberOfPages = CEIL($numberOfVideos/$input['limit']);
        }
        $listVideos = $listVideos->get();
        return [
            'error' => false,
            'data' => [
                'numberOfVideos' => $numberOfVideos,
                'numberOfPages' => $numberOfPages,
                'listVideos' => $listVideos
            ],
            'errorCode' => null
        ];
    }
}
