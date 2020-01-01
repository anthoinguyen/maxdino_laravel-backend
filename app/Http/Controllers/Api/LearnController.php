<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Learn;
use DB;

class LearnController extends BaseApiController
{
    /**
     * @SWG\Post(
     *      path="/learn",
     *      operationId="createPostLearn",
     *      tags={"Learns"},
     *      summary="Create Post Learn",
     *      description="Create Post Learn",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="learnTitle",
     *      description="Title to upload",
     *      in="formData",
     *      type="string",
     *      ),
     *      @SWG\Parameter(
     *      name="learnContent",
     *      description="Content to upload",
     *      in="formData",
     *      type="string",
     *      ),
     *      @SWG\Parameter(
     *      name="learnPriority",
     *      description="Priority to upload",
     *      in="formData",
     *      type="number",
     *      ),
     *      @SWG\Parameter(
     *      name="learnImage",
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
            $validator = Learn::validate($input, 'Rule_Create_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $input['learnImage'] = $this->saveImage($request, 'learn');
            $post = Learn::createPost($request->user->id, $input);
            //Response Success
            return $this->responseSuccess($post);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Delete(
     *      path="/learn/{learnId}",
     *      tags={"Learns"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="learnId",
     *          description="description",
     *          required=true,
     *          type="number",
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
    public function deletePost(Request $request, $learnId)
    {
        try {
            //Input
            $input['learnId'] = $learnId;
            //Validation
            $validator = Learn::validate($input, 'Rule_Delete_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $post = Learn::find($learnId);
            if (!$post) {
                return $this->responseErrorCustom('learn_not_found', 404);
            }
            $post->delete();
            //Response Success
            $result = "Delete successfully!";
            return $this->responseSuccess($result);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Get(
     *      path="/learn/{learnId}",
     *      tags={"Learns"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="learnId",
     *          description="description",
     *          required=true,
     *          type="number",
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
    public function getPost(Request $request, $learnId)
    {
        try {
            //Input
            $input['learnId'] = $learnId;
            //Validation
            $validator = Learn::validate($input, 'Rule_Get_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $post = Learn::find($learnId);
            if (!$post) {
                return $this->responseErrorCustom('learn_not_found', 404);
            }
            //Response Success
            $user = $post->user()->first();
            $post->setAttribute('username', $user->username);
            $post->setAttribute('avatar', $user->avatar);
            return $this->responseSuccess($post);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Post(
     *      path="/learn/{learnId}",
     *      operationId="UpdateLearn",
     *      tags={"Learns"},
     *      summary="Update Learn",
     *      description="Update Learn",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="learnId",
     *      description="learnId to upload",
     *      in="path",
     *      type="number",
     *      required=true,
     *      ),
     *      @SWG\Parameter(
     *      name="learnTitle",
     *      description="Title to upload",
     *      in="formData",
     *      type="string",
     *      ),
     *      @SWG\Parameter(
     *      name="learnContent",
     *      description="Content to upload",
     *      in="formData",
     *      type="string",
     *      ),
     *      @SWG\Parameter(
     *      name="learnPriority",
     *      description="Priority to upload",
     *      in="formData",
     *      type="number",
     *      ),
     *      @SWG\Parameter(
     *      name="learnImage",
     *      description="Image to upload",
     *      in="formData",
     *      type="file",
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Not Found"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     */
    public function updatePost(Request $request, $learnId)
    {
        try {
            //Input
            $input = $request->all();
            $input['learnId'] = $learnId;
            //Validation
            $validator = Learn::validate($input, 'Rule_Update_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $post = Learn::find($input['learnId']);
            if (!$post) {
                return $this->responseErrorCustom('learn_not_found', 404);
            }
            if ($request->hasFile('learnImage')) {
                $input['learnImage'] = $this->saveImage($request, 'learn');
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
     *      path="/learn/all",
     *      tags={"Learns"},
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
     *      description="Ex: id, username, title, content, priority, created_at, updated_at",
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
     *      description="Ex: id, username, title, content, priority, created_at, updated_at",
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
     *
     */
    public function getAllPost(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Validation
            $validator = Learn::validate($input, 'Rule_Get_All_Post');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $result = Learn::getAllPost($input);
            if ($result['error']){
                $this->responseErrorCustom($result['errorCode']);
            }
            //Response Success
            return $this->responseSuccess($result['data']);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
}
