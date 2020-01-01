<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Schema;
use App\Traits\FullTextSearch;

class Learn extends BaseModel
{
    //
    use FullTextSearch;
    protected $table = 'learns';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'content', 'image', 'priority'
    ];
    /**
     * The columns of the full text index
     */
    protected $searchable = [
        '`learns`.`title`', '`learns`.`content`',
    ];

    public static $rules = array(
        'Rule_Create_Post' => [
            'learnTitle' => 'required|string|max:100',
            'learnContent' => 'required|string|max:5000',
            'learnImage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'learnPriority' => 'required|numeric|between:1,10',
        ],
        'Rule_Get_Post' => [
            'learnId' => 'required|integer',
        ],
        'Rule_Delete_Post' => [
            'learnId' => 'required|integer'
        ],
        'Rule_Update_Post' => [
            'learnId' => 'required|integer',
            'learnTitle' => 'string|max:100',
            'learnContent' => 'string|max:5000',
            'learnImage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'learnPriority' => 'numeric|between:1,10',
        ],
        'Rule_Get_All_Post' => [
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
        return $query->join('users', 'learns.user_id', '=', 'users.id')
        ->select(['learns.*', 'users.username', 'users.avatar']);
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function createPost($userId, $input = array())
    {
        $post = Learn::create([
            'user_id' => $userId,
            'title' => $input['learnTitle'],
            'content' => $input['learnContent'],
            'image' => $input['learnImage'],
            'priority' => $input['learnPriority'],
        ]);
        $user = $post->user()->first();
        $post->setAttribute('username', $user->username);
        $post->setAttribute('avatar', $user->avatar);
        return $post;
    }
    public function updatePost($input)
    {
        if (isset($input['learnTitle'])) {
            $this->title = $input['learnTitle'];
        }
        if (isset($input['learnContent'])) {
            $this->content = $input['learnContent'];
        }
        if (isset($input['learnPriority'])) {
            $this->priority = $input['learnPriority'];
        }
        if (isset($input['learnImage'])) {
            $this->image = $input['learnImage'];
        }
        $this->save();
        $user = $this->user()->first();
        $this->setAttribute('username', $user->username);
        $this->setAttribute('avatar', $user->avatar);
    }

    public static function getAllPost($input)
    {
        $field = [
            'id' => 'learns.id',
            'username' => 'users.username',
            'title' => 'learns.title',
            'content' => 'learns.content',
            'priority' => 'learns.priority',
            'created_at' => 'learns.created_at',
            'updated_at' => 'learns.updated_at'
        ];
        $listPosts = Learn::GetDetail();
        
        if (isset($input['fieldSort']) && isset($input['typeSort'])) {
            if(!array_key_exists($input['fieldSort'], $field))
            {
                return [
                    'error' => true,
                    'data' => [],
                    'errorCode' => 'fieldSort_not_found'
                ];
            }
            $listPosts->orderBy($field[$input['fieldSort']], $input['typeSort']);
            if ($input['fieldSort'] != 'id')
            {
                $listPosts->orderBy('id', 'desc');
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
            $listPosts->where($field[$input['fieldSearch']],'like', '%' . $input['keySearch'] . '%');
            
        }

        $numberOfPosts = $listPosts->count();
        $numberOfPages = 1;
        if (isset($input['limit'])) {
            $listPosts->limit($input['limit']);
            if (isset($input['offset'])) {
                $listPosts->offset($input['offset']);
            }
            $numberOfPages = CEIL($numberOfPosts/$input['limit']);
        }
        $listPosts = $listPosts->get();
        return [
            'error' => false,
            'data' => [
                'numberOfPosts' => $numberOfPosts,
                'numberOfPages' => $numberOfPages,
                'listPosts' => $listPosts
            ],
            'errorCode' => null
        ];
    }
    public static function searchCustom($query)
    {
        $searchableTerm = Learn::fullTextWildcards($query);
        $result = Learn::join('users', 'learns.user_id', '=', 'users.id')
        ->select(['learns.*',
        'users.username',
        'users.avatar',
        DB::raw("MATCH (`learns`.`title`, `learns`.`content`) AGAINST ('".$searchableTerm."' IN BOOLEAN MODE) AS relevance_score")
        ])
        ->whereRaw("MATCH (`learns`.`title`, `learns`.`content`) AGAINST (? IN BOOLEAN MODE)", $searchableTerm)
        ->orderByDesc('relevance_score');
        return $result;
    }
}
