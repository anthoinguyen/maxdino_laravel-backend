<?php

namespace App\Http\Controllers;

use App\User;
use App\Ask;
use App\Learn;
use App\Video;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use JWTAuth;
use JWTFactory;
use App\Notifications\CreateUserRequest;
use App\Notifications\CreateUserSuccess;
use Carbon\Carbon;
use DB;

class AdminController extends BaseApiController
{
    public function requestCreateUser(Request $request)
    {
        /**
         * @SWG\Post(
         *     path="/admin/request/create-user",
         *     description="Send email attach token",
         *     tags={"Admin"},
         *     summary="Send request create user",
         *     security={{"jwt":{}}},
         *      @SWG\Parameter(
         *          name="body",
         *          description="Email to send invitation",
         *          required=true,
         *          in="body",
         *          @SWG\Schema(
         *              @SWG\Property(
         *                  property="username",
         *                  type="string",
         *              ),
         *              @SWG\Property(
         *                  property="email",
         *                  type="string",
         *              ),
         *              @SWG\Property(
         *                  property="admin",
         *                  type="boolean",
         *              ),
         *          ),
         *      ),
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=403, description="Forbidden"),
         *      @SWG\Response(response=404, description="Not Found"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         * )
         */

        try {

            $validator = User::validate($request->all(), 'Rule_RequestCreateUser');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            } else {
                $email = $request->email;
                $user = User::where(['email' => $email])->first();
                if ($user) {
                    return $this->responseErrorCustom("users_available", 422); // Unprocessable Entity
                }

                $user = new User;
                $user->email = $email;
                $user->avatar = 'upload/user/image/avatar/default.png';
                $user->active = 0;

                $request->username ?  $user->username = $request->username : $user->username = "No name";
                $request->admin ? $user->admin = 1 : $user->admin = 0;;
                $user->password = bcrypt(str_random(6));

                //create token
                $factory = JWTFactory::customClaims([
                    'sub' => $email,
                    'exp' => Carbon::now()->addMinutes(60 * 24 * 30 * 365),
                ]);
                $payload = $factory->make();
                $token = JWTAuth::encode($payload);
                if ($token) {
                    $user->notify(
                        new CreateUserRequest($token, $request->admin)
                    );
                    $user->save();
                    return $this->responseSuccess($user);
                }
            }
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function acceptCreateUser(Request $request)
    {
        /**
         * @SWG\Post(
         *     path="/admin/accept/create-user",
         *     description="Finish create user",
         *     tags={"Admin"},
         *     summary="Accept request create user",
         *
         *      @SWG\Parameter(
         *          name="body",
         *          description="Create user",
         *          required=true,
         *          in="body",
         *          @SWG\Schema(
         *              @SWG\property(
         *                  property="token",
         *                  type="string",
         *              ),
         *              @SWG\property(
         *                  property="username",
         *                  type="string",
         *              ),
         *              @SWG\property(
         *                  property="password",
         *                  type="string",
         *              ),
         *              @SWG\Property(
         *                  property="confirmPassword",
         *                  type="string",
         *              ),
         *          ),
         *      ),
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=404, description="Not Found"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         * )
         */

        try {
            $validator = User::validate($request->all(), 'Rule_AcceptCreateUser');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }
            $token = $request->token;
            $payload = JWTAuth::setToken($token)->getPayload();
            if (!$payload) {
                return $this->responseErrorCustom("tokens_create_user_invalid", 401);
            }

            $email = $payload->get('sub');
            $user = User::where(['email' => $email, 'active' => 0])
                ->update([
                    'username' => $request->username,
                    'password' => bcrypt($request->password),
                    'active' => 1,
                    'avatar' => 'upload/user/image/avatar/default.png',
                ]);

            if (!$user) {
                return $this->responseErrorCustom("tokens_create_user_invalid");
            }

            User::where(['email' => $email, 'active' => 1])->first()
                ->notify(
                    new CreateUserSuccess()
                );
            return $this->responseSuccess("Create successfully");
        } catch (\Exception $exception) {
            if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return BaseApiController::responseErrorCustom("tokens_create_user_invalid", 401);
            } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return BaseApiController::responseErrorCustom("tokens_expired", 401);
            } else {
                return BaseApiController::responseErrorCustom("tokens_not_found", 401);
            }
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function deleteUser(Request $request)
    {
        /**
         * @SWG\Delete(
         *     path="/admin/{id}",
         *     description="Delete user",
         *     tags={"Admin"},
         *     summary="Delete user",
         *     security={{"jwt":{}}},
         *      @SWG\Parameter(
         *         description="ID user to delete",
         *         in="path",
         *         name="id",
         *         required=true,
         *         type="integer",
         *         format="int64"
         *     ),
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=403, description="Forbidden"),
         *      @SWG\Response(response=404, description="Not Found"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         * )
         */

        try {
            $validator = User::validate(["userId" => $request->id], 'Rule_DeleteUser');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }

            $userId = $request->id; //only for easy to under what is $request->id.
            $user = User::where(['id' => $userId])->first();
            if (!$user) {
                return $this->responseErrorCustom("users_not_found", 404);
            }

            $countAdmin = User::where(['admin' => 1])->count();
            if ($countAdmin <= 1 && $user->admin == true) {
                return $this->responseErrorCustom("can_not_delete_user", 403); //Forbidden
            }

            $user->delete();
            return $this->responseSuccess("Delete user successfully");
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function getAllUser(Request $request)
    {
        /**
         * @SWG\Get(
         *     path="/admin/all-user",
         *     description="Get list users",
         *     tags={"Admin"},
         *     summary="Get list users",
         *     security={{"jwt":{}}},
         *       @SWG\Parameter(
         *          name="page",
         *          description="Query page",
         *          in="query",
         *          type="number"
         *      ),
         *       @SWG\Parameter(
         *          name="limit",
         *          description="Query limit records per page",
         *          in="query",
         *          type="number"
         *      ),
         *
         *      @SWG\Parameter(
         *          name="searchBy",
         *          description="Query searchBy column",
         *          in="query",
         *          type="string"
         *      ),
         *
         *      @SWG\Parameter(
         *          name="keyword",
         *          description="Query search",
         *          in="query",
         *          type="string"
         *      ),
         *
         *      @SWG\Parameter(
         *          name="sortBy",
         *          description="Query sortBy column",
         *          in="query",
         *          type="string"
         *      ),
         *
         *       @SWG\Parameter(
         *          name="sort",
         *          description="Query sort",
         *          in="query",
         *          type="string"
         *      ),
         *
         *       @SWG\Parameter(
         *          name="active",
         *          description="Query active",
         *          in="query",
         *          type="boolean"
         *      ),
         *       @SWG\Parameter(
         *          name="admin",
         *          description="Query admin",
         *          in="query",
         *          type="boolean"
         *      ),
         *
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=403, description="Forbidden"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         * )
         */

        try {
            $input = $request->all();

            if (isset($input['active'])) {
                $input['active'] == 'true' ? $input['active'] = 1 : $input['active'] = 0;
            }
            if (isset($input['admin'])) {
                $input['admin'] == 'true' ? $input['admin'] = 1 : $input['admin'] = 0;
            }

            $validator = User::validate($input, 'Rule_Get_All_User');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }


            $results = User::funcGetAllUser($input);

            if ($results['error']) {
                return $this->responseErrorCustom($results['errorCode']);
            }
            return $this->responseSuccess($results['data']);

        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function getStatistic()
    {
        /**
         * @SWG\Get(
         *     path="/admin/statistic",
         *     description="Get Statistic",
         *     tags={"Admin"},
         *     summary="Get Statistic",
         *     security={{"jwt":{}}},
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=403, description="Forbidden"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         * )
         */

        try {
            $result = [
                'account' => User::count(),
                'ask' =>  Ask::count(),
                'learn' => Learn::count(),
                'video' => Video::count(),
            ];

            return $this->responseSuccess($result);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }

    public function editUser(Request $request)
    {
        /**
         * @SWG\Put(
         *     path="/admin/{id}",
         *     description="Edit user",
         *     tags={"Admin"},
         *     summary="Edit user",
         *     security={{"jwt":{}}},
         *      @SWG\Parameter(
         *         description="ID user to edit",
         *         in="path",
         *         name="id",
         *         required=true,
         *         type="integer",
         *         format="int64"
         *     ),
         *      @SWG\Parameter(
         *          name="body",
         *          description="Create user",
         *          required=true,
         *          in="body",
         *          @SWG\Schema(
         *              @SWG\Property(
         *                  property="admin",
         *                  type="boolean",
         *              ),
         *              @SWG\Property(
         *                  property="username",
         *                  type="string",
         *              ),
         *              @SWG\Property(
         *                  property="active",
         *                  type="boolean",
         *              ),
         *          ),
         *      ),
         *      @SWG\Response(response=200, description="Successful"),
         *      @SWG\Response(response=401, description="Unauthorized"),
         *      @SWG\Response(response=403, description="Forbidden"),
         *      @SWG\Response(response=404, description="Not Found"),
         *      @SWG\Response(response=422, description="Unprocessable Entity"),
         *      @SWG\Response(response=500, description="Internal Server Error"),
         * )
         */

        try {
            $input = $request->all();
            $input['userId'] = $request->id;

            $validator = User::validate($input, 'Rule_EditUser');
            if ($validator) {
                return $this->responseErrorValidator($validator, 422);
            }

            $userId = $request->id;
            $user = User::where(['id' => $userId])->first();
            if (!$user) {
                return $this->responseErrorCustom("users_not_found", 404);
            }

            $countAdmin = User::where(['admin' => 1])->count();
            if ($countAdmin <= 1 && $request->admin == false && $user->admin == true) {
                return $this->responseErrorCustom("can_not_edit_user", 403); //min number of admin is 1
            }

            $request->admin ?  $user->admin = 1 : $user->admin = 0;
            $request->active ?  $user->active = 1 : $user->active = 0;
            $user->username = $request->username;
            $user->save();
            return $this->responseSuccess($user);
        } catch (\Exception $exception) {
            return $this->responseErrorException($exception->getMessage(), 99999, 500);
        }
    }
}
