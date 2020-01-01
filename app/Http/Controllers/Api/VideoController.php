<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Video;

class VideoController extends BaseApiController
{
    /**
     * @SWG\Post(
     *      path="/video",
     *      operationId="createvideo",
     *      tags={"Videos"},
     *      summary="Create video",
     *      description="Create video",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *      name="videoTitle",
     *      description="Title video",
     *      required=true,
     *      in="formData",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="videoLink",
     *      description="Link video",
     *      required=true,
     *      in="formData",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="videoImage",
     *      description="Image Video to upload",
     *      in="formData",
     *      type="file",
     *      ),
     *      @SWG\Parameter(
     *      name="videoPriority",
     *      description="Priority Video",
     *      required=true,
     *      in="formData",
     *      type="number",
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     */
    public function createVideo(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Validation
            $validator = Video::validate($input, 'Rule_Create_Video');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $input['linkImage'] = $this->saveImage($request, 'video');
            $video = Video::createVideo($request->user->id, $input);
            //Response Success
            return $this->responseSuccess($video);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Delete(
     *      path="/video/{videoId}",
     *      tags={"Videos"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="videoId",
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
    public function deleteVideo(Request $request, $videoId)
    {
        try {
            //Input
            $input['videoId'] = $videoId;
            //Validation
            $validator = Video::validate($input, 'Rule_Delete_Video');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $video = Video::find($videoId);
            if (!$video) {
                return $this->responseErrorCustom('video_not_found', 404);
            }
            $video->delete();
            //Response Success
            $result = "Delete Successful!!";
            return $this->responseSuccess($result);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Get(
     *      path="/video/{videoId}",
     *      tags={"Videos"},
     *      description="description",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      @SWG\Parameter(
     *          name="videoId",
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
    public function getVideo(Request $request, $videoId)
    {
        try {
            //Input
            $input['videoId'] = $videoId;
            //Validation
            $validator = Video::validate($input, 'Rule_Get_Video');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $video = Video::find($videoId);
            if (!$video) {
                return $this->responseErrorCustom('video_not_found', 404);
            }
            //Response Success
            $user = $video->user()->first();
            $video->setAttribute('username', $user->username);
            $video->setAttribute('avatar', $user->avatar);
            return $this->responseSuccess($video);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Post(
     *      path="/video/{videoId}",
     *      operationId="UpdateVideo",
     *      tags={"Videos"},
     *      summary="Update Video",
     *      security={
     *       {"jwt": {"*"}},
     *      },
     *      description="Update Video",
     *      @SWG\Parameter(
     *      name="videoId",
     *      description="videoId to upload",
     *      in="path",
     *      type="number",
     *      required=true,
     *      ),
     *      @SWG\Parameter(
     *      name="videoTitle",
     *      description="Title video",
     *      in="formData",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="videoLink",
     *      description="Link video",
     *      in="formData",
     *      type="string"
     *      ),
     *      @SWG\Parameter(
     *      name="videoImage",
     *      description="Image Video to upload",
     *      in="formData",
     *      type="file",
     *      ),
     *      @SWG\Parameter(
     *      name="videoPriority",
     *      description="Priority Video",
     *      in="formData",
     *      type="number",
     *      ),
     *      @SWG\Response(response=200, description="Successful"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Not Found"),
     *      @SWG\Response(response=422, description="Unprocessable Entity"),
     *      @SWG\Response(response=500, description="Internal Server Error"),
     *     )
     */
    public function updateVideo(Request $request, $videoId)
    {
        try {
            //Input
            $input = $request->all();
            $input['videoId'] = $videoId;
            //Validation
            $validator = Video::validate($input, 'Rule_Update_Video');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $video = Video::find($input['videoId']);
            if (!$video) {
                return $this->responseErrorCustom('video_not_found', 404);
            }
            if ($request->hasFile('videoImage')) {
                $input['linkImage'] = $this->saveImage($request, 'video');
            }
            $video->updateVideo($input);
            //Response Success
            return $this->responseSuccess($video);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
    /**
     * @SWG\Get(
     *      path="/video/all",
     *      tags={"Videos"},
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
     *      description="Ex: id, username, title, link, priority, created_at, updated_at",
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
     *      description="Ex: id, username, title, link, priority, created_at, updated_at",
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
    public function getAllVideo(Request $request)
    {
        try {
            //Input
            $input = $request->all();
            //Validation
            $validator = Video::validate($input, 'Rule_Get_All_Video');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            //Main Code
            $result = Video::getAllVideo($input);
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
