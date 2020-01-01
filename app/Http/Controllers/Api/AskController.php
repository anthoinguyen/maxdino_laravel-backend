<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ask;
use App\Comment;
use App\Reaction;
use DB;

class AskController extends BaseApiController
{
    /**
     * @SWG\Post(
     *      path="/ask",
     *      operationId="createPostAsk",
     *      tags={"Asks"},
     *      summary="Create Post Ask",
     *      description="Create Post Ask",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="askContent",
     *      description="Content post ask",
     *      in="formData",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="askImage",
     *      description="Image to upload",
     *      in="formData",
     *      type="file",
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     *
     */
    public function createPost(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Validation
            $validator = Ask::validate($input, 'Rule_Create_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $input['askImage'] = $this->saveImage($request, 'ask');
            $post = Ask::createPost(
                $input,
                $request->user->id,
            );
            //Response Success
            return $this->responseSuccess($post);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Delete(
     *      path="/ask/{askId}",
     *      tags={"Asks"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="askId",
     *          description="description",
     *          required=true,
     *          type="string",
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
    public function deletePost(Request $request, $askId)
    {
        try {
            //Input
            $input['askId'] = $askId;
            //Validation
            $validator = Ask::validate($input, 'Rule_Delete_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $post = Ask::find($input['askId']);
            if (!$post) {
                return $this->responseErrorCustom('ask_not_found', 404);
            }
            if ($request->user->id != $post->user_id && !$request->user->admin) {
                return $this->responseErrorCustom('user_priority', 403);
            }
            Comment::where(["ask_id" => $askId])->delete();
            Reaction::where(["ask_id" => $askId])->delete();
            $post->delete();
            //Response Success
            return $this->responseSuccess("Delete Successfully!");
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Get(
     *      path="/ask/{askId}",
     *      tags={"Asks"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="askId",
     *          description="description",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Not Found"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     *
     */
    public function getPost(Request $request, $askId)
    {
        try {
            //Input
            $input['askId'] = $askId;
            //Validation
            $validator = Ask::validate($input, 'Rule_Get_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $post = Ask::find($askId);
            if (!$post) {
                return $this->responseErrorCustom('ask_not_found', 404);
            }
            $user = $post->user()->first();
            $post->setAttribute('username', $user->username);
            //Response Success
            return $this->responseSuccess($post);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Post(
     *      path="/ask/{askId}",
     *      operationId="UpdatePost",
     *      tags={"Asks"},
     *      summary="Update Post",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="askId",
     *      description="askId to update",
     *      in="path",
     *      type="number",
     *      required=true,
     *      ),
     *      @SWG\Parameter(
     *      name="askContent",
     *      description="content to update",
     *      in="formData",
     *      type="string",
     *      ),
     *      @SWG\Parameter(
     *      name="askImage",
     *      description="Image to update",
     *      in="formData",
     *      type="file",
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
    public function updatePost(Request $request, $askId)
    {
        try {
            //Input
            $input = $request->all();
            $input['askId'] = $askId;
            //Validation
            $validator = Ask::validate($input, 'Rule_Update_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $post = Ask::find($request->askId);
            if (!$post) {
                return $this->responseErrorCustom('ask_not_found', 404);
            }
            if ($request->user->id != $post->user_id && !$request->user->admin) {
                return $this->responseErrorCustom("user_priority", 403);
            }
            if ($request->hasFile('askImage')) {
                $input['askImage'] = $this->saveImage($request, 'ask');
            }
            $post->updatePost($input);
            //Response Success
            return $this->responseSuccess($post);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Get(
     *      path="/ask/all",
     *      tags={"Asks"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="offset",
     *      description="Query offset",
     *      in="query",
     *      type="number"
     *      ),
     *      @SWG\Parameter(
     *      name="limit",
     *      description="Query limit",
     *      in="query",
     *      type="number"
     *      ),
     *      @SWG\Parameter(
     *      name="fieldSort",
     *      description="Ex: id, username, content, created_at, updated_at",
     *      in="query",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="typeSort",
     *      description="Ex: asc, desc",
     *      in="query",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="fieldSearch",
     *      description="Ex: id, username, content, created_at, updated_at",
     *      in="query",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="keySearch",
     *      description="Key Search",
     *      in="query",
     *      type="string"
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     */
    public function getAllPost(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Validation
            $validator = Ask::validate($input, 'Rule_Get_All_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $result = Ask::getAllPost($input, $request->user->id);
            if ($result['error']) {
                $this->responseErrorCustom($result['errorCode']);
            }
            //Response Success
            return $this->responseSuccess($result['data']);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    /**
     * @SWG\Get(
     *      path="/ask/news-feed",
     *      tags={"Asks"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="offset",
     *      description="Query offset",
     *      in="query",
     *      type="number"
     *      ),
     *      @SWG\Parameter(
     *      name="limit",
     *      description="Query limit",
     *      in="query",
     *      type="number"
     *      ),
     *      @SWG\Parameter(
     *      name="sort",
     *      description="Query sort",
     *      in="query",
     *      type="string"
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     *
     */
    public function getNewsFeed(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Main Code
            $result = Ask::getNewsFeed($input, $request->user->id);
            //Response Success
            return $this->responseSuccess($result);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    /**
     * @SWG\Post(
     *      path="/ask/reaction/{askId}",
     *      tags={"Asks"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="askId",
     *          description="description",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Not Found"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     */
    public function reactionPost(Request $request, $askId)
    {
        //Input
        $input['askId'] = $askId;
        //Validation
        $validator = Ask::validate($input, 'Rule_Reaction_Post');
        if ($validator) {
            return $this->responseErrorValidator($validator, 422);
        }
        //Main Code
        $post = Ask::find($askId);
        if (!$post) {
            return $this->responseErrorCustom('ask_not_found', 404);
        }
        $reaction = $post->reactionAsk($request->user->id);
        //Response Success
        return $this->responseSuccess($reaction);
    }
}
