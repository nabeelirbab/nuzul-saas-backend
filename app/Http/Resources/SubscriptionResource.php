<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $startTime = Carbon::parse($this->start_date);
        $finishTime = Carbon::parse($this->end_date);
        $totalDuration = $finishTime->diffInDays($startTime);

        return [
            'id' => $this->id,
            'orders' => $this->orders->map(function ($item) {
                return [
                    'id' => $item->id,
                    'total_amount_without_tax' => $item->total_amount_without_tax,
                    'total_amount_with_tax' => $item->total_amount_with_tax,
                    'type' => $item->type,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            }),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'is_trial' => $this->is_trial,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'days_left' => $totalDuration,
        ];
    }
}
