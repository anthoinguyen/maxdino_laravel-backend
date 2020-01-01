<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reaction;
use App\Comment;
use App\Learn;
use App\Ask;
use App\User;
use DB;
use File;

class UserController extends BaseApiController
{

    public function getReactionsActivities(Request $request)
    {
        /**
         * @SWG\Get(
         *      path="/user/reactions-activities",
         *      tags={"Users"},
         *      description="Get reactions activities",
         *      security={
         *       {"jwt": {"*"}},
         *      },
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         *     )
         *
         */
        try {
            $reaction = Reaction::select(
                DB::raw("'liked' as type"),
                "users.username",
                "users.avatar",
                "asks.content as askContent",
                "reactions.updated_at"
            )
                ->where(['status' => 1])
                ->join('users', 'users.id', '=', 'reactions.user_id')
                ->join('asks', 'asks.id', '=', 'reactions.ask_id')
                ->orderBy('reactions.updated_at', 'desc')
                ->first();
            return $this->responseSuccess($reaction);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function getCommentsActivities(Request $request)
    {
        /**
         * @SWG\Get(
         *      path="/user/comments-activities",
         *      tags={"Users"},
         *      description="Get comments activities",
         *      security={
         *       {"jwt": {"*"}},
         *      },
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         *     )
         *
         */
        try {
            $comments = Comment::select(
                DB::raw("'commented' as type"),
                "users.username",
                "users.avatar",
                "asks.content as askContent",
                "comments.updated_at"
            )
                ->orderBy('updated_at', 'desc')
                ->join('users', 'users.id', '=', 'comments.user_id')
                ->join('asks', 'asks.id', '=', 'comments.ask_id')
                ->first();
            return $this->responseSuccess($comments);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function getAllActivities(Request $request)
    {
        /**
         * @SWG\Get(
         *      path="/user/all-activities",
         *      tags={"Users"},
         *      description="Get all activities",
         *      security={
         *       {"jwt": {"*"}},
         *      },
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         *     )
         *
         */
        try {
            $comments = Comment::select(
                DB::raw("'commented' as type"),
                "users.username",
                "users.avatar",
                "asks.content as askContent",
                "comments.updated_at"
            )
                ->join('users', 'users.id', '=', 'comments.user_id')
                ->join('asks', 'asks.id', '=', 'comments.ask_id');

            $reaction = Reaction::select(
                DB::raw("'liked' as type"),
                "users.username",
                "users.avatar",
                "asks.content as askContent",
                "reactions.updated_at"
            )
                ->where(['status' => 1])
                ->join('users', 'users.id', '=', 'reactions.user_id')
                ->join('asks', 'asks.id', '=', 'reactions.ask_id')
                ->union($comments)
                ->orderBy("updated_at", "desc")
                ->limit(5)
                ->get();

            return $this->responseSuccess($reaction);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    /**
     * @SWG\Get(
     *      path="/user/search",
     *      tags={"Users"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="q",
     *      description="Query key search",
     *      in="query",
     *      type="string"
     *      ),
     *      @SWG\Response(response=200, description="Success"),
     *      @SWG\Response(response=400, description="Invalid request params"),
     *      @SWG\Response(response=401, description="Request is not authenticated"),
     *      @SWG\Response(response=404, description="Not Found"),
     *     )
     *
     */
    public function search(Request $request)
    {
        try {
            $resultAsk = Ask::searchCustom($request->q, $request->user->id)->get();
            // $resultAsk = Ask::search($request->q)->GetDetail($request->user->id)->get();
            $sumResultAsk = $resultAsk->count();
            $resultAsk = $resultAsk->slice(0, 5);
            $resultLearn = Learn::searchCustom($request->q)->get();
            //$resultLearn = Learn::search($request->q)->GetDetail()->get();
            $sumResultLearn = $resultLearn->count();
            $resultLearn = $resultLearn->slice(0, 5);
            $result = [
                'resultAsk' => $resultAsk->toArray(),
                'sumResultAsk' => $sumResultAsk,
                'resultLearn' => $resultLearn->toArray(),
                'sumResultLearn' => $sumResultLearn,
            ];
            //return
            return $this->responseSuccess($result);
        } catch (\Exception $exception) {
            //return
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function changeUserAvatar(Request $request)
    {
        /**
         * @SWG\Post(
         *      path="/user/change-avatar",
         *      operationId="changeUserAvatar",
         *      tags={"Users"},
         *      summary="Change user's avatar",
         *      description="Change user's avatar",
         *      security={
         *       {"jwt": {"*"}},
         *      },
         *      @SWG\Parameter(
         *      name="avatar",
         *      description="Image to upload",
         *      in="formData",
         *      type="file",
         *      required=true,
         *      ),
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=404, description="Not Found"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         *     )
         *
         */

        try {
            $validator = User::validate($request->all(), 'Rule_Change_UserAvatar'); //add rule
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $request->user->avatar = $this->saveImage($request, 'user', $request->user->avatar);
            $request->user->save();
            return $this->responseSuccess($request->user);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function getProfile(Request $request)
    {
        /**
         * @SWG\Get(
         *      path="/user/profile",
         *      tags={"Users"},
         *      description="Get user profile",
         *      security={
         *       {"jwt": {"*"}},
         *      },
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         *     )
         *
         */
        try {
            return $this->responseSuccess($request->user);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
}
