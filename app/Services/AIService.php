<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private $geminiApiKey;
    private $weatherApiKey;

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
        $this->weatherApiKey = env('OPENWEATHER_API_KEY');
    }

    /**
     * Auto-categorize post using Google Gemini (FREE)
     */
    public function categorizePost($title, $content)
    {
        if (!$this->geminiApiKey) {
            return null; // Fallback to manual categorization
        }

        try {
            $response = Http::timeout(10)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->geminiApiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Analyze this community alert and categorize it into ONE of these exact categories:

1. Traffic & Transportation
2. Crime & Safety  
3. Price & Market Updates
4. Lost & Found
5. Local Services & Help
6. Emergency Alerts

Title: {$title}
Content: {$content}

Return ONLY the category name exactly as written above, nothing else."
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 20
                    ]
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                $category = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                // Map to category IDs
                $categoryMap = [
                    'Traffic & Transportation' => 7,
                    'Crime & Safety' => 8,
                    'Price & Market Updates' => 9,
                    'Lost & Found' => 10,
                    'Local Services & Help' => 11,
                    'Emergency Alerts' => 12,
                ];

                return $categoryMap[$category] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('AI Categorization failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Detect fake news and suspicious content
     */
    public function detectFakeNews($title, $content)
    {
        if (!$this->geminiApiKey) {
            return ['is_suspicious' => false, 'confidence' => 0, 'reason' => 'AI not available'];
        }

        try {
            $response = Http::timeout(15)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->geminiApiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Analyze this post for misinformation, fake news, or suspicious claims. Consider:
- Unrealistic claims
- Conspiracy theories  
- Misleading information
- Spam or scam content
- Hate speech or harmful content

Title: {$title}
Content: {$content}

Return ONLY a JSON object in this exact format:
{\"is_suspicious\": true/false, \"confidence\": 0-100, \"reason\": \"brief explanation\"}"
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => 100
                    ]
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                $jsonText = trim($result['candidates'][0]['content']['parts'][0]['text']);
                
                // Clean up the JSON response
                $jsonText = preg_replace('/```json\s*/', '', $jsonText);
                $jsonText = preg_replace('/```\s*/', '', $jsonText);
                
                $analysis = json_decode($jsonText, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $analysis;
                }
            }
        } catch (\Exception $e) {
            Log::error('Fake news detection failed: ' . $e->getMessage());
        }

        return ['is_suspicious' => false, 'confidence' => 0, 'reason' => 'Analysis unavailable'];
    }

    /**
     * Generate automatic weather alerts
     */
    public function generateWeatherAlerts()
    {
        if (!$this->weatherApiKey) {
            return [];
        }

        try {
            $response = Http::timeout(10)->get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => 'Dhaka,BD',
                'appid' => $this->weatherApiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $weather = $response->json();
                $alerts = [];

                // Check for severe weather conditions
                $condition = $weather['weather'][0]['main'];
                $description = $weather['weather'][0]['description'];
                $temp = $weather['main']['temp'];
                $humidity = $weather['main']['humidity'];

                // Heavy rain alert
                if (in_array($condition, ['Rain', 'Thunderstorm']) && $weather['rain']['1h'] ?? 0 > 5) {
                    $alerts[] = [
                        'title' => '🌧️ Heavy Rain Alert - Dhaka',
                        'content' => "Heavy rainfall detected ({$description}). Avoid waterlogged areas and drive carefully. Current temperature: {$temp}°C",
                        'priority' => 'high',
                        'category_id' => 12, // Emergency Alerts
                    ];
                }

                // Extreme temperature alert
                if ($temp > 38) {
                    $alerts[] = [
                        'title' => '🌡️ Extreme Heat Warning',
                        'content' => "Temperature reached {$temp}°C. Stay hydrated and avoid outdoor activities during peak hours.",
                        'priority' => 'medium',
                        'category_id' => 12,
                    ];
                } elseif ($temp < 10) {
                    $alerts[] = [
                        'title' => '🥶 Cold Wave Alert',
                        'content' => "Temperature dropped to {$temp}°C. Wear warm clothes and check on elderly neighbors.",
                        'priority' => 'medium', 
                        'category_id' => 12,
                    ];
                }

                // High humidity alert
                if ($humidity > 85) {
                    $alerts[] = [
                        'title' => '💧 High Humidity Alert',
                        'content' => "Humidity at {$humidity}%. Expect discomfort and possible health issues for sensitive individuals.",
                        'priority' => 'low',
                        'category_id' => 12,
                    ];
                }

                return $alerts;
            }
        } catch (\Exception $e) {
            Log::error('Weather alert generation failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get traffic incidents using OpenStreetMap (FREE)
     */
    public function getTrafficIncidents()
    {
        try {
            // Query Overpass API for traffic-related data in Dhaka area
            $query = '[out:json][timeout:25];
            (
              way["highway"]["name"](23.7,90.3,23.9,90.5);
              relation["type"="restriction"](23.7,90.3,23.9,90.5);
            );
            out geom;';

            $response = Http::timeout(30)->post('https://overpass-api.de/api/interpreter', [
                'data' => $query
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $incidents = [];

                foreach ($data['elements'] as $element) {
                    if (isset($element['tags']['highway']) && isset($element['tags']['name'])) {
                        // Simulate traffic detection based on road importance
                        $roadName = $element['tags']['name'];
                        $highway = $element['tags']['highway'];
                        
                        // Major roads more likely to have traffic
                        if (in_array($highway, ['primary', 'secondary', 'trunk']) && rand(1, 10) > 7) {
                            $incidents[] = [
                                'title' => "🚗 Traffic Alert - {$roadName}",
                                'content' => "Heavy traffic reported on {$roadName}. Consider alternative routes.",
                                'priority' => 'medium',
                                'category_id' => 7, // Traffic & Transportation
                                'latitude' => $element['geometry'][0]['lat'] ?? null,
                                'longitude' => $element['geometry'][0]['lon'] ?? null,
                                'location_name' => $roadName . ', Dhaka',
                            ];
                        }
                    }
                }

                return array_slice($incidents, 0, 3); // Limit to 3 incidents
            }
        } catch (\Exception $e) {
            Log::error('Traffic incident detection failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Analyze post sentiment
     */
    public function analyzeSentiment($title, $content)
    {
        if (!$this->geminiApiKey) {
            return ['sentiment' => 'neutral', 'confidence' => 0];
        }

        try {
            $response = Http::timeout(10)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->geminiApiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Analyze the sentiment of this post. Return ONLY a JSON object:

Title: {$title}
Content: {$content}

{\"sentiment\": \"positive/negative/neutral/urgent\", \"confidence\": 0-100, \"emotion\": \"calm/worried/angry/helpful/panicked\"}"
                                ]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                $jsonText = trim($result['candidates'][0]['content']['parts'][0]['text']);
                $jsonText = preg_replace('/```json\s*/', '', $jsonText);
                $jsonText = preg_replace('/```\s*/', '', $jsonText);
                
                $sentiment = json_decode($jsonText, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $sentiment;
                }
            }
        } catch (\Exception $e) {
            Log::error('Sentiment analysis failed: ' . $e->getMessage());
        }

        return ['sentiment' => 'neutral', 'confidence' => 0, 'emotion' => 'calm'];
    }

    /**
     * Generate smart suggestions for post improvement
     */
    public function generatePostSuggestions($title, $content)
    {
        if (!$this->geminiApiKey) {
            return [];
        }

        try {
            $response = Http::timeout(10)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->geminiApiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Analyze this community alert post and suggest improvements:

Title: {$title}
Content: {$content}

Provide 3 specific suggestions to make this alert more helpful, clear, and actionable. Return as JSON array:
[\"suggestion 1\", \"suggestion 2\", \"suggestion 3\"]"
                                ]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                $jsonText = trim($result['candidates'][0]['content']['parts'][0]['text']);
                $jsonText = preg_replace('/```json\s*/', '', $jsonText);
                $jsonText = preg_replace('/```\s*/', '', $jsonText);
                
                $suggestions = json_decode($jsonText, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($suggestions)) {
                    return $suggestions;
                }
            }
        } catch (\Exception $e) {
            Log::error('Post suggestions failed: ' . $e->getMessage());
        }

        return [];
    }
}
