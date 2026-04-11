<?php

return [
    'workflow_review_notification_email' => env('WORKFLOW_REVIEW_NOTIFICATION_EMAIL', 'michael.zgq@gmail.com'),
    'marketing_hosts' => array_values(array_filter(array_map('trim', explode(',', env('MARKETING_HOSTS', 'dossentry.com,www.dossentry.com'))))),
    'public_demo_hosts' => array_values(array_filter(array_map('trim', explode(',', env('PUBLIC_DEMO_HOSTS', 'demo.dossentry.com,demo.relayoffice.ai'))))),
    'internal_admin_login_url' => env('INTERNAL_ADMIN_LOGIN_URL', 'https://relayoffice-returns-app.onrender.com/admin/auth/login'),
    'guest_demo' => [
        'email' => env('GUEST_DEMO_EMAIL', 'guest@dossentry.com'),
        'password' => env('GUEST_DEMO_PASSWORD', '12345678'),
        'workspace_label' => env('GUEST_DEMO_WORKSPACE_LABEL', 'Shared guest workspace'),
        'disclaimer' => env('GUEST_DEMO_DISCLAIMER', 'Sample data only. Resets regularly.'),
    ],
    'sample_brand_review_return_id' => env('SAMPLE_BRAND_REVIEW_RETURN_ID', 'RMA-1003'),
];
