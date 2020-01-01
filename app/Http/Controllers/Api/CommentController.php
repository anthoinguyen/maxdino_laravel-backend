<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentController extends BaseApiController
{
    /**
     * @SWG\Post(
     *      path="/comment",
     *      operationId="createComment",
     *      tags={"Comment"},
     *      summary="Create Comment",
     *      description="Create Comment",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="body",
     *      description="Create Comment",
     *      required=true,
     *      in="body",
     *      @SWG\Schema(
     *              @SWG\property(
     *                  property="askId",
     *                  type="number",
     *              ),
     *              @SWG\property(
     *                  property="commentContent",
     *                  type="string",
     *              ),
     *          ),
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     *
     */
    public function createComment(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Validation
            $validator = Comment::validate($input, 'Rule_Create_Comment');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $result = Comment::createComment($request->user->id, $input);
            //Response Success
            return $this->responseSuccess($result);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Delete(
     *      path="/comment/{commentId}",
     *      tags={"Comment"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="commentId",
     *          description="description",
     *          required=true,
     *          type="number",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=403, description="Forbidden"),
     *      @SWG\Response(response=404, description="Not Found"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     *
     */
    public function deleteComment(Request $request, $commentId)
    {
        try {
            //Input
            $input['commentId'] = $commentId;
            //Validation
            $validator = Comment::validate($input, 'Rule_Delete_Comment');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $comment = Comment::find($input['commentId']);
            if (!$comment) {
                return $this->responseErrorCustom('comment_not_found', 404);
            }
            if ($comment->user_id != $request->user->id && !$request->user->admin){
                return $this->responseErrorCustom('user_priority', 403);
            }
            $comment->delete();
            //Response Success
            return $this->responseSuccess("Delete successfully!");
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Put(
     *      path="/comment/{commentId}",
     *      operationId="UpdateComment",
     *      tags={"Comment"},
     *      summary="Update Comment",
     *      description="Update Comment",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="commentId",
     *      description="commentId to update",
     *      in="path",
     *      type="number",
     *      required=true,
     *      ),
     *      @SWG\Parameter(
     *      name="commentContent",
     *      description="content to update",
     *      in="formData",
     *      type="string",
     *      required=true,
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=403, description="Forbidden"),
     *      @SWG\Response(response=404, description="Not Found"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     */
    public function updateComment(Request $request, $commentId)
    {
        try {
            //Input
            $input = $request->all();
            $input['commentId'] = $commentId;
            //Validation
            $validator = Comment::validate($input, 'Rule_Update_Comment');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $comment = Comment::find($request->commentId);
            if (!$comment) {
                return $this->responseErrorCustom('comment_not_found', 404);
            }
            if ($comment->user_id != $request->user->id) {
                return $this->responseErrorCustom('user_priority', 403);
            }
            $comment->content = $request->commentContent;
            $comment->save();
            $user = $comment->user()->first();
            $comment->setAttribute('username', $user->username);
            $comment->setAttribute('avatar', $user->avatar);
            //Response Success
            return $this->responseSuccess($comment);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Get(
     *      path="/comment/ask/{askId}",
     *      tags={"Comment"},
     *      description="Get comment ask",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="askId",
     *      description="askId",
     *      in="path",
     *      type="number"
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     *
     */
    public function getCommentAsk(Request $request, $askId)
    {
        try {
            //Input
            $input = $request->all();
            $input['askId'] = $askId;
            //Validation
            $validator = Comment::validate($input, 'Rule_Get_Comment_Ask');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $comments = Comment::getCommentAsk($input['askId']);
            //Response Success
            return $this->responseSuccess($comments);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
}
