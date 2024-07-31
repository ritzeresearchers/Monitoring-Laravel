<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class UserController extends Controller
{
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomers(Request $request): JsonResponse
    {
        $params = $request->all();
        $customersQuery = $this->userRepository->getCustomerItemsOverview($params);
        $customersCount= $this->userRepository->getFilteredModelCount($params);

        return response()->json([
            'customers' => CustomerResource::collection($customersQuery->get()),
            'count' => $customersCount,
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return CustomerResource
     */
    public function updateCustomer(
        Request $request,
        User    $user
    ): CustomerResource
    {
        $user->update($request->only([
            'name',
            'first_name',
            'last_name',
            'is_active',
            'status',
        ]));

        return CustomerResource::make($user);
    }

    public function deleteEntity(User $user): JsonResponse
    {
        try {
            if ($user->business()->exists()) {
                $user->business()->first()->delete();
            }

            $user->delete();
        } catch (Throwable $throwable) {
            return $this->respondError($throwable->getMessage());
        }

        return response()->json(['status' => 'success'], ResponseAlias::HTTP_NO_CONTENT);
    }
}
