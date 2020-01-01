<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Comment extends BaseModel
{
    //
    protected $table = 'comments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ask_id', 'content'
    ];
    public static $rules = array(
        'Rule_Create_Comment' => [
            'askId' => 'required|integer',
            'commentContent' => 'required|string|max:3000',
        ],
        'Rule_Delete_Comment' => [
            'commentId' => 'required|integer',
        ],
        'Rule_Update_Comment' => [
            'commentId' => 'required|integer',
            'commentContent' => 'required|string|max:3000',
        ],
        'Rule_Get_Comment_Ask' => [
            'askId' => 'required|integer',
        ],
    );
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public static function createComment($userId, $input = array())
    {
        $comment = Comment::create([
            'user_id' => $userId,
            'ask_id' => $input['askId'],
            'content' => $input['commentContent'],
        ]);
        $user = $comment->user()->first();
        $comment->setAttribute('username', $user->username);
        $comment->setAttribute('avatar', $user->avatar);
        $numberOfComments = Comment::where('ask_id', '=', $input['askId'])->count();
        return [
            'comment' => $comment,
            'numberOfComments' => $numberOfComments
        ];
    }
    public static function getCommentAsk($askId)
    {
        $allPosts = Comment::where('ask_id', '=', $askId)
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->limit(5)
            ->orderBy('updated_at', 'desc')
            ->select(['comments.*', 'users.username', 'users.avatar']);
        $allPosts = $allPosts->get();
        return $allPosts;
    }
}
