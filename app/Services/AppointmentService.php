<?php
namespace App\Services;

use App\Models\Doctor;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;

class AppointmentService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Doctor::class;
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'department' => ['sometimes', 'string']
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'department' => ['sometimes','nullable', 'string']
        ];
    }
}
?>
