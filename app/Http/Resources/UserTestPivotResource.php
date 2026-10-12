<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTestPivotResource extends JsonResource
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
            'testId' => $this->parameters->test_id,
            'userPhoto' => $this->parameters->use_photo,
            'practiceAttempts' => $this->parameters->use_train,
            'attempts' => $this->parameters->attempts
        ];
    }
}
