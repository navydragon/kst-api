<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
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
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'division' => is_null($this->division) ? " ": $this->division ,
            'position' => is_null($this->job) ? " ": $this->job ,
            'phone' => $this->phone,
            'email' => $this->email,
            'login' => $this->login,
            'valid_till' => is_null($this->valid_till) ? " ": $this->valid_till ,
            //'plainPassword' => $this->password_plain,
            'filial' => new FilialResource($this->filial),
            'role' => new RoleResource($this->role),
            'test' => new UserTestPivotResource($this->tests()->first()),
        ];
    }
}
