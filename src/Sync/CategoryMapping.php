<?php

declare(strict_types=1);

namespace App\Sync;

class CategoryMapping
{
    public const string DETECTION_MASK = 'detectionMask';
    public const string CATEGORY = 'category';
    public const string ENVELOPE = 'envelope';
    public const string ENVELOPE_NAME = 'name';
    public const string CATEGORY_NAME = 'name';
    public const string CATEGORY_ENVELOPE_NAME = 'envelope';
    public const string CATEGORY_DETECTIONS = 'detections';
    public const string CATEGORY_DETECTION_MASK = 'mask';
    public const string CATEGORY_DETECTION_CONFIDENCE = 'confidence';
    public const int DEFAULT_CONFIDENCE = 100;
}
