<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Get campaign detail
     *
     * @param Request $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Request $request, Campaign $campaign)
    {
        if ((!$subscriber = \App\Models\Subscriber::where('ip', \App\Helpers\Ip::getRequestIp())->where('campaign_id', $campaign->id)->first()
            or (!$subscriber->is_subscribed and !$request->cookie('showed')))
            and !$request->cookie('showed')
        ) {

            $uuid = ($subscriber ? $subscriber->uuid : \Illuminate\Support\Str::uuid());
            if ($request->cookie('uuid')) {
                $uuid = $request->cookie('uuid');
                if ($existingSubscriber = Subscriber::where('uuid', $uuid)->first()
                    and $existingSubscriber->is_subscribed
                ) {
                    return response('');
                }
            }

            // Return view
            return response(view('campaign', [
                'campaign' => $campaign,
                'subscriber' => $subscriber,
                'uuid' => $uuid
            ]), 200, [
                'Content-Type' => 'text/javascript'
            ])->cookie('uuid', $uuid, 24*60*365)
                ->cookie('showed', 1, 60);
        }

        $responseContent = '(function() {})()';
        if ($subscriber) {
            $responseContent .= '/*'.$subscriber->id.'*/';
        }
        $response = response($responseContent)->withHeaders([
            'Content-Type' => 'text/javascript'
        ]);

        if ($subscriber and $subscriber->is_subscribed) {
            $response->cookie('uuid', $subscriber->uuid, 24*60*365);
        }

        return $response;
    }
}
