<?php

namespace App\Services\Eloquent;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\ITransformService;
use App\Models\Eloquent\Transform;
use GuzzleHttp\Client;

class TransformService implements ITransformService
{
    /**
     * @param string $relationType
     * @param int $relationId
     * @param string $targetSystem
     *
     * @return ServiceResponse
     */
    public function getTargetValue(
        string $relationType,
        int    $relationId,
        string $targetSystem
    )
    {
        $targetValue = Transform::where('relation_type', $relationType)
            ->where('relation_id', $relationId)
            ->where('target_system', $targetSystem)->first();
        if ($targetValue) {
            return new ServiceResponse(
                true,
                'Target value',
                200,
                $targetValue->target_value
            );
        } else {
            return new ServiceResponse(
                false,
                'Target value not found',
                404,
                null
            );
        }
    }
}
