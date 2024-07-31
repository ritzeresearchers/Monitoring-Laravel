<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait ControllerHelpers
{
    /**
     * @param $mobileNumber
     * @return string|void
     */
    public function getMobileNumberValidationError($mobileNumber)
    {
        if (substr($mobileNumber, 0, 2) != config('constants.countryCode')) {
            return 'Invalid mobile number country code.';
        }
        if (!isValidMobileNumber($mobileNumber)) {
            return 'Invalid mobile number.';
        }
    }

    /**
     * @param string $currentEmail
     * @param string $confirmationEmail
     * @return string|void
     */
    public function getConfirmationEmailValidationError(string $currentEmail, string $confirmationEmail)
    {
        $user = Auth::user();
        $existingUser = User::firstWhere('email', $currentEmail);

        if ($existingUser && $user->email !== $existingUser->email) {
            return 'Email is already in used.';
        }
        if ($currentEmail !== $confirmationEmail) {
            return 'Email did not match.';
        }
    }

    /**
     * @param Request $request
     * @return string|void
     */
    public function getJobPropertiesValidationError(Request $request)
    {
        if (!in_array($request->get('job_type'), array_keys(config('constants.jobType')))) {
            return 'Invalid job type.';
        }
        if (!in_array($request->get('target_job_done'), array_keys(config('constants.targetJobDone')))) {
            return 'Invalid target completion.';
        }
        if (config('constants.targetJobDone.specificDate') === $request->get('target_job_done') && empty($request->specificJobDoneDatetime)) {
            return 'Missing specificJobDoneDatetime parameter.';
        }
    }

    /**
     * @param Request $request
     * @return string|null
     */
    public function getTargetCompletionDatetime(Request $request): ?string
    {
        if (config('constants.targetJobDone.specificDate') === $request->get('target_job_done')) {
            return Carbon::parse($request->get('specificJobDoneDatetime'))->format('Y-m-d H:i:s');
        }
        return null;
    }
}
