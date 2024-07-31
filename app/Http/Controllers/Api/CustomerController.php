<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RegisterCustomerRequest;
use App\Events\CustomerUserRegistered;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    use ControllerHelpers;

    public function register(RegisterCustomerRequest $request): JsonResponse
    {
       
        $data = $request->validated();
        // if (isset($data['name'])) {
        //     list($firstName, $lastName) = explode(' ', $data['name']);
        // } else {
        //     $firstName = $request->get('firstName');
        //     $lastName = $request->get('lastName');
        //     $data['name'] = $firstName . ' ' . $lastName;
        // }


        $verificationCode = Str::random(10);

        $user = User::create([
            // 'title' => $request->title,
            // 'first_name'        => $firstName,
            // 'last_name'         => $lastName,
            // 'name'              => $data['name'],
            'title' => $request->title,
            'first_name' => $request->first_name,
            'last_name' => $request->middle_name,
            'middle_name' => $request->last_name,
            'email'             => $data['email'],
            'mobile_number'     => $data['mobile_number'],
            'mobile_number_verified_at' => now(),
            'password'          => bcrypt($data['password']),
            'verification_code' => $verificationCode,
            'user_type'         => config('constants.accountType.customer'),
        ]);

        foreach (config('constants.notificationChannelTypes') as $channelType) {
            $user->notificationChannels()->create(
                ['channel' => $channelType, 'is_enabled' => true]
            );
        }

        foreach (config('constants.notifiableEvents') as $event) {
            $user->notifiableEvents()->create(
                ['event' => $event, 'is_enabled' => true]
            );
        }
        //  Todo enable this event for sending email
        // event(new CustomerUserRegistered([
        //     'first_name'        => 'first_name',
        //     'last_name'         => 'last_Name',
        //     'email'             => $data['email'],
        //     'verification_code' => $verificationCode,
        //     // 'name'              => $data['name'],
        //     'user_type'         => config('constants.accountType.customer'),
        // ]));

        if ($request->has([
            'title',
            'description',
            'category_id',
            'service_id',
            'location',
            'job_type',
            'rate_type',
            'specificJobDoneDatetime',
            'target_job_done'
        ])) {
            $user->jobs()->create(array_merge($request->only([
                'service_id',
                'category_id',
                'title',
                'description',
                'job_type',
                'rate_type',
                'target_job_done',
            ]), [
                'location_id'                => Location::firstWhere('location', $request->get('location'))->id,
                'target_completion_datetime' => $this->getTargetCompletionDatetime($request),
                'status'                     => config('constants.jobStatus.pending'),
            ]));
        }

        return response()->json();
    }
}
