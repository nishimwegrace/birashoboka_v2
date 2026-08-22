<?php

namespace App\Controllers;

use App\Models\Campaign;
use App\Models\Volet;
use App\Validators\Validator;
use Illuminate\Database\Capsule\Manager as DB;

class CampaignController extends Controller
{
    public static function index(): void
    {
        $query = Campaign::with(['volet', 'activity']);
        self::applySearchAndSort($query, ['title', 'description', 'edition', 'place'], ['start_date', 'registration_start', 'created_at']);
        self::paginate($query, 'Campaigns retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'volet_id'           => 'required|exists:volets,id',
            'activity_id'        => 'nullable|exists:activities,id',
            'edition'            => 'required|string',
            'title'              => 'required|string',
            'description'        => 'required|string',
            'registration_start' => 'nullable|date',
            'registration_end'   => 'nullable|date',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date',
            'place'              => 'nullable|string',
            'quota'              => 'nullable|integer',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        self::checkDateRanges($body);

        $campaign = DB::transaction(function () use ($body) {
            $volet = Volet::find($body['volet_id']);
            $place = $body['place'] ?? $volet->place;

            return Campaign::create([
                'volet_id'           => $body['volet_id'],
                'activity_id'        => $body['activity_id'] ?? null,
                'edition'            => trim($body['edition']),
                'title'              => trim($body['title']),
                'description'        => trim($body['description']),
                'registration_start' => $body['registration_start'] ?? null,
                'registration_end'   => $body['registration_end'] ?? null,
                'start_date'         => $body['start_date'] ?? null,
                'end_date'           => $body['end_date'] ?? null,
                'place'              => trim($place),
                'is_open'            => isset($body['is_open']) ? (bool) $body['is_open'] : true,
                'quota'              => isset($body['quota']) ? (int) $body['quota'] : null,
            ]);
        });

        apiResponse(true, 'Campaign created successfully', $campaign->load(['volet', 'activity']), 201);
    }

    public static function show(int $id): void
    {
        $campaign = Campaign::with(['volet', 'activity', 'inscriptions'])->find($id);
        if (!$campaign) {
            apiResponse(false, 'Campaign not found', null, 404);
        }

        apiResponse(true, 'Campaign retrieved successfully', $campaign);
    }

    public static function update(int $id, array $body): void
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            apiResponse(false, 'Campaign not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'volet_id'           => 'nullable|exists:volets,id',
            'activity_id'        => 'nullable|exists:activities,id',
            'edition'            => 'nullable|string',
            'title'              => 'nullable|string',
            'description'        => 'nullable|string',
            'registration_start' => 'nullable|date',
            'registration_end'   => 'nullable|date',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date',
            'place'              => 'nullable|string',
            'quota'              => 'nullable|integer',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        self::checkDateRanges($body, $campaign);

        DB::transaction(function () use ($campaign, $body) {
            if (isset($body['volet_id']))    $campaign->volet_id    = $body['volet_id'];
            if (isset($body['activity_id'])) $campaign->activity_id = $body['activity_id'];
            foreach (['edition', 'title', 'description', 'registration_start', 'registration_end', 'start_date', 'end_date'] as $field) {
                if (isset($body[$field])) $campaign->{$field} = $body[$field];
            }
            if (isset($body['place']))   $campaign->place   = trim($body['place']);
            elseif (isset($body['volet_id'])) $campaign->place = Volet::find($body['volet_id'])->place;
            if (isset($body['is_open'])) $campaign->is_open = (bool) $body['is_open'];
            if (isset($body['quota']))   $campaign->quota   = (int) $body['quota'];
            $campaign->save();
        });

        apiResponse(true, 'Campaign updated successfully', $campaign->load(['volet', 'activity']));
    }

    public static function destroy(int $id): void
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            apiResponse(false, 'Campaign not found', null, 404);
        }

        $campaign->delete();
        apiResponse(true, 'Campaign deleted successfully', null, 200);
    }

    private static function checkDateRanges(array $body, Campaign $campaign = null): void
    {
        $registrationStart = $body['registration_start'] ?? $campaign?->registration_start;
        $registrationEnd   = $body['registration_end']   ?? $campaign?->registration_end;
        $startDate         = $body['start_date']         ?? $campaign?->start_date;
        $endDate           = $body['end_date']           ?? $campaign?->end_date;

        if ($registrationStart && $registrationEnd && strtotime($registrationStart) > strtotime($registrationEnd)) {
            apiResponse(false, 'Validation failed', ['registration_start' => ['The registration start date must be before or equal to registration end date.']], 422);
        }
        if ($startDate && $endDate && strtotime($startDate) > strtotime($endDate)) {
            apiResponse(false, 'Validation failed', ['start_date' => ['The start date must be before or equal to end date.']], 422);
        }
    }
}
