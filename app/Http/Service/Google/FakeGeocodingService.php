<?php

namespace App\Http\Service\Google;

class FakeGeocodingService extends GeocodingService
{
    public function getAddressBySearchString(string $searchString): array
    {
        $response = [
            "results" => [
                [
                    "address_components" => [
                        [
                            "long_name" => "1600",
                            "short_name" => "1600",
                            "types" => ["street_number"]
                        ],
                        [
                            "long_name" => "Amphitheatre Pkwy",
                            "short_name" => "Amphitheatre Pkwy",
                            "types" => ["route"]
                        ],
                        [
                            "long_name" => "Mountain View",
                            "short_name" => "Mountain View",
                            "types" => ["locality", "political"]
                        ],
                        [
                            "long_name" => "Santa Clara County",
                            "short_name" => "Santa Clara County",
                            "types" => ["administrative_area_level_2", "political"]
                        ],
                        [
                            "long_name" => "California",
                            "short_name" => "CA",
                            "types" => ["administrative_area_level_1", "political"]
                        ],
                        [
                            "long_name" => "United States",
                            "short_name" => "US",
                            "types" => ["country", "political"]
                        ],
                        [
                            "long_name" => "94043",
                            "short_name" => "94043",
                            "types" => ["postal_code"]
                        ]
                    ],
                    "formatted_address" => "1600 Amphitheatre Parkway, Mountain View, CA 94043, USA",
                    "geometry" => [
                        "location" => [
                            "lat" => 37.4224764,
                            "lng" => -122.0842499
                        ],
                        "location_type" => "ROOFTOP",
                        "viewport" => [
                            "northeast" => [
                                "lat" => 37.4238253802915,
                                "lng" => -122.0829009197085
                            ],
                            "southwest" => [
                                "lat" => 37.4211274197085,
                                "lng" => -122.0855988802915
                            ]
                        ]
                    ],
                    "place_id" => "ChIJ2eUgeAK6j4ARbn5u_wAGqWA",
                    "plus_code" => [
                        "compound_code" => "CWC8+W5 Mountain View, California, United States",
                        "global_code" => "849VCWC8+W5"
                    ],
                    "types" => ["street_address"]
                ],
                [
                    "address_components" => [
                        [
                            "long_name" => "Kyiv",
                            "short_name" => "Kyiv",
                            "types" => [
                                "locality",
                                "political"
                            ]
                        ],
                        [
                            "long_name" => "Kyiv City",
                            "short_name" => "Kyiv City",
                            "types" => [
                                "administrative_area_level_2",
                                "political"
                            ]
                        ],
                        [
                            "long_name" => "Ukraine",
                            "short_name" => "UA",
                            "types" => [
                                "country",
                                "political"
                            ]
                        ],
                        [
                            "long_name" => "02000",
                            "short_name" => "02000",
                            "types" => [
                                "postal_code"
                            ]
                        ]
                    ],
                    "formatted_address" => "Kyiv, Ukraine, 02000",
                    "geometry" => [
                        "bounds" => [
                            "northeast" => [
                                "lat" => 50.590798,
                                "lng" => 30.825941
                            ],
                            "southwest" => [
                                "lat" => 50.213273,
                                "lng" => 30.2394401
                            ]
                        ],
                        "location" => [
                            "lat" => 50.4501,
                            "lng" => 30.5234
                        ],
                        "location_type" => "APPROXIMATE",
                        "viewport" => [
                            "northeast" => [
                                "lat" => 50.590798,
                                "lng" => 30.825941
                            ],
                            "southwest" => [
                                "lat" => 50.213273,
                                "lng" => 30.2394401
                            ]
                        ]
                    ],
                    "place_id" => "ChIJBUVa4U7P1EAR_kYBF9IxSXY",
                    "types" => [
                        "locality",
                        "political"
                    ]
                ]
            ],
            "status" => "OK"
        ];
        return LocationInfoModel::parseFromJson($response);
    }
}
