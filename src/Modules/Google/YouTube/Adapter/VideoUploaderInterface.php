<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use App\Modules\Google\YouTube\Entity\ClientConfiguration;

interface VideoUploaderInterface
{
    public function upload(Video $video, ClientConfiguration $clientConfiguration): void;
}
