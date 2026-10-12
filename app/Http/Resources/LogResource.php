<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
          'id' =>$this->id,
          'level'=> $this->level,
          'message' => $this->message,
          'extra' => $this->extra,
          'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
    }
}
