<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    | Set these in your .env file (or Render environment variables):
    |
    |   CLOUDINARY_CLOUD_NAME=your_cloud_name
    |   CLOUDINARY_API_KEY=your_api_key
    |   CLOUDINARY_API_SECRET=your_api_secret
    |
    | Get these from: https://console.cloudinary.com → Settings → API Keys
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'    => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
];
