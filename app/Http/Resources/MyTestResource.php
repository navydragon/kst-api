<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyTestResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'link' => $this->link,
            'countQuestions' => $this->questions,
            'countAttempts' => $this->parameters->attempts,
            'usePhoto' => $this->parameters->use_photo,
            'useTrain' => $this->parameters->use_train,
            'currentPercent' => $this->parameters->current_percent,
            'currentScore' => $this->parameters->current_score,
            'isPassed' => $this->parameters->is_passed,
            'passingScore' => $this->passing_score,
            'passingPercent' => $this->passing_percent,
            'hasPhoto' => (is_null($this->parameters->photo_file)) ? false : true,
            'attempts' => AttemptResource::collection($this->user_attempts($this->parameters->user_id)) 
        ];
    }
}
