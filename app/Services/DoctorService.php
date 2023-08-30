<?php
namespace App\Services;

use App\Models\Doctor;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;

class DoctorService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Doctor::class;
    }
}
?>
