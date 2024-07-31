<?php

return [
   'businessBaseNumber' => 1000000,
   'leadBaseNumber' => 1000000,
   'jobBaseNumber' => 1000000,

   'locationTypes' => [
      'coordinates' => 'coordinates',
      'address' => 'address'
   ],

   'accountType' => [
      'customer' => 'customer',
      'business' => 'business',
      'admin' => 'admin'
   ],

   'businessUserRole' => [
      'owner' => 'owner',
      'member' => 'member',
   ],

   'targetJobDone' => [
      'immediate' => 'immediate',
      'flexible' => 'flexible',
      'specificDate' => 'specificDate'
   ],

   'jobType' => [
     'residential' => 'residential',
     'commercial' => 'commercial'
   ],

   'jobStatus' => [
      'pending' => 'pending',
      'open' => 'open',  // still looking for contractor
      'active' => 'active',  //already accepted a contractor
      'accepted' => 'accepted',
      'inProgress' => 'inProgress', //already accepted a contractor
      'finished' => 'finished',
      'cancelled' => 'cancelled',
      'archived' => 'archived',
   ],

   'quoteStatus' => [
      'pending' => 'pending',
      'accepted' => 'accepted',
      'declined' => 'declined',
      'rejected' => 'rejected',
      'notInterested' => 'notInterested',
      'cancelled' => 'cancelled',
   ],

   'leadStatus' => [
      'pending' => 'pending',
      'active' => 'active',
      'notInterested' => 'notInterested',
      'accepted' => 'accepted',
   ],

   'deadlineAdjustmentStatus' => [
      'pending' => 'pending',
      'accepted' => 'accepted',
      'rejected' => 'rejected',
   ],

   'bargainStatus' => [
      'pending' => 'pending',
      'accepted' => 'accepted',
      'rejected' => 'rejected',
   ],

   'rateType' => [
      'flat' => 'flat',
      'hourly' => 'hourly'
   ],

   'notificationChannelTypes' => [
      'email' => 'email',
      'sms' => 'sms'
   ],

   'notifiableEvents' => [
      'messages' => 'messages',
      'newsletters' => 'newsletters',
      'specialOffers' => 'spencialOffers',
      'recommendations' => 'recommendations',
      'newJobLeads' => 'newJobLeads'
   ],

   'businessNotifiableEvents' => [
      'messages' => 'messages',
      'newsletters' => 'newsletters',
      'specialOffers' => 'spencialOffers',
      'newJobLeads' => 'newJobLeads'
   ],

   'messageTypes' => [
      'text' => 'text',
      'uploadImage' => 'uploadImage',
      'uploadFile' => 'uploadFile',
      'calendarScheduling' => 'calendarScheduling',
      'calendarSchedulingResponse' => 'calendarSchedulingResponse',
      'startProject' => 'startProject', // accepted or declined
      'startProjectOptionResult' => 'startProjectOptionResult', // accepted or declined
      'bargainCostEstimate' => 'bargainCostEstimate', //
      'changeQuoteStatus' => 'changeQuoteStatus',
      'bargain' => 'bargain',
      'bargainAcceptance' => 'bargainAcceptance',
      'bargainRateTypeResult' => 'bargainRateTypeResult',  // hourly or flat
      'bargainCostEstimateResult' => 'bargainCostEstimateResult',  //
      'changeDeadline' => 'changeDeadline',
   ],

   'leadLocationCoverage' => [
      'all' => 'all',
      'selected' => 'selected',
   ],

   'countryCode' =>  env('COUNTRY_CODE', 254),

    'possibleApplicationEnvs' => [
        'staging' => 'staging',
        'production' => 'production',
        'local' => 'local'
    ],

    'applicationEnv' => [
        'currentEnv' => env('APP_ENV', 'local')
    ]
];
