<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Schema;
use App\Traits\FullTextSearch;

class Ask extends BaseModel
{
    use FullTextSearch;
    //
    protected $table = 'asks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'content', 'image',
    ];
    /**
     * The columns of the full text index
     */
    protected $searchable = [
        '`asks`.`content`',
    ];

    public static $rules = array(
        'Rule_Create_Post' => [
            'askContent' => 'required_without:askImage|string|max:1000',
            'askImage' => 'required_without:askContent|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],
        'Rule_Get_Post' => [
            'askId' => 'required|integer',
        ],
        'Rule_Update_Post' => [
            'askId' => 'required|integer',
            'askContent' => 'required_without:askImage|string|max:1000',
            'askImage' => 'required_without:askContent|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],
        'Rule_Get_All_Post' => [
            'offset' => 'integer',
            'limit' => 'integer',
            'fieldSort' => 'string',
            'typeSort' => 'in:asc,desc',
            'fieldSearch' => 'string',
            'keySearch' => 'string',
        ],
        'Rule_Delete_Post' => [
            'askId' => 'required|integer',
        ],
        'Rule_Reaction_Post' => [
            'askId' => 'required|integer',
        ],
    );

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function scopeGetDetail($query, $userId)
    {
        return $query->join('users', 'asks.user_id', '=', 'users.id')
        ->leftjoin('comments', 'comments.ask_id', '=', 'asks.id')
        ->leftjoin('reactions',
        function($join)
        {
            $join->on('reactions.ask_id', '=', 'asks.id')
            ->where('reactions.status', '=', 1);
        })
        ->groupBy('asks.id')
        ->select(['asks.*','users.username', 'users.avatar',
        DB::raw('count(DISTINCT reactions.id) as countReactions'),
        DB::raw('count(DISTINCT comments.id) as countComments'),
        DB::raw('count(DISTINCT reactions.id And IF(reactions.user_id ='.$userId.', 1, NULL)) as userReaction')]);
    }

    public static function createPost($input = array(), $userId)
    {
        $post = new Ask;
        $post->user_id = $userId;
        $post->content = (isset($input['askContent'])?$input['askContent']:null);
        $post->image = (isset($input['askImage'])?$input['askImage']:null);
        $post->save();

        //Custom response
        $user = $post->user()->first();
        $post->setAttribute('username', $user->username);
        $post->setAttribute('avatar', $user->avatar);
        $post->setAttribute('countReactions', 0);
        $post->setAttribute('countComments', 0);
        $post->setAttribute('userReaction', 0);
        return $post;
    }
    
    public function updatePost($input = array())
    {
        if(isset($input['askContent']))
        {
            $this->content = $input['askContent'];
        }
        if(isset($input['askImage']))
        {
            $this->image = $input['askImage'];
        }
        $this->save();
        //Custom response
        $user = $this->user()->first();
        $this->setAttribute('username', $user->username);
    }

    public function reactionAsk($userId)
    {
        $check = Reaction::where([
            'user_id' => $userId,
            'ask_id' => $this->id
        ])->first();
        if ($check) {
            if ($check->status) {
                $check->status = false;
                $check->save();
                return false;
            }
            $check->status = true;
            $check->save();
            return true;
        }
        Reaction::create([
            'user_id' => $userId,
            'ask_id' => $this->id,
            'status' => true
        ]);
        //Custom response
        return true;
    }

    public static function getNewsFeed($input, $userId)
    {
        $listPosts = Ask::getDetail($userId);
        if (isset($input['limit'])) {
            $listPosts->limit($input['limit']);
            if (isset($input['offset'])) {
                $listPosts->offset($input['offset']);
            }
        }
        if (isset($input['sort'])) {
            $listPosts->orderBy('updated_at', $input['sort']);
        }
        $listPosts = $listPosts->get();
        return $listPosts;
    }

    public static function searchCustom($query, $userId)
    {
        $searchableTerm = Ask::fullTextWildcards($query);
        $result = Ask::join('users', 'asks.user_id', '=', 'users.id')
        ->leftjoin('comments', 'comments.ask_id', '=', 'asks.id')
        ->leftjoin('reactions',
        function($join)
        {
            $join->on('reactions.ask_id', '=', 'asks.id')
            ->where('reactions.status', '=', 1);
        })
        ->groupBy('asks.id')
        ->select(['asks.*','users.username', 'users.avatar',
        DB::raw('count(DISTINCT reactions.id) as countReactions'),
        DB::raw('count(DISTINCT comments.id) as countComments'),
        DB::raw('count(DISTINCT reactions.id And IF(reactions.user_id ='.$userId.', 1, NULL)) as userReaction'),
        DB::raw("MATCH (`asks`.`content`) AGAINST ('".$searchableTerm."' IN BOOLEAN MODE) AS relevance_score")
        ])
        ->whereRaw("MATCH (`asks`.`content`) AGAINST (? IN BOOLEAN MODE)", $searchableTerm)
        ->orderByDesc('relevance_score');
        return $result;
    }
    
    public static function getAllPost($input, $userId)
    {
        $field = [
            'id' => 'asks.id',
            'username' => 'users.username',
            'content' => 'asks.content',
            'created_at' => 'asks.created_at',
            'updated_at' => 'asks.updated_at'
        ];
        $listPosts = Ask::GetDetail($userId);
        
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
        
        $sub = $listPosts;
        $numberOfPosts = DB::table( DB::raw("({$sub->toSql()}) as sub"))
        ->mergeBindings($sub->getQuery())
        ->count('sub.id');
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
}
