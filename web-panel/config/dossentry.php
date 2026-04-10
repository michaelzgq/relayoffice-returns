<?php

return [
    'workflow_review_notification_email' => env('WORKFLOW_REVIEW_NOTIFICATION_EMAIL', 'solutionsoscommerce@gmail.com'),
    'guest_demo' => [
        'email' => env('GUEST_DEMO_EMAIL', 'ops@admin.com'),
        'password' => env('GUEST_DEMO_PASSWORD', '12345678'),
        'workspace_label' => env('GUEST_DEMO_WORKSPACE_LABEL', 'Shared guest workspace'),
        'disclaimer' => env('GUEST_DEMO_DISCLAIMER', 'Sample data only. Resets regularly.'),
    ],
    'sample_brand_review_return_id' => env('SAMPLE_BRAND_REVIEW_RETURN_ID', 'RMA-1003'),
];
