<?php

$access_key = "YOUR_ACCESS_KEY";
$secret_key = "YOUR_SECRET_KEY";
$associate_tag = "YOUR_ASSOCIATE_TAG";
$product_id = "YOUR_PRODUCT_ID";

$base_url = "http://webservices.amazon.com/onca/xml?";

$params = [
    "Service" => "AWSECommerceService",
    "Operation" => "ItemLookup",
    "ItemId" => $product_id,
    "ResponseGroup" => "Reviews",
    "AWSAccessKeyId" => $access_key,
    "AssociateTag" => $associate_tag,
    "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
];

// Sort the parameters in lexicographic order
ksort($params);

// Generate the canonical query string
$canonical_query_string = "";
foreach ($params as $key => $value) {
    $canonical_query_string .= "&" . rawurlencode($key) . "=" . rawurlencode($value);
}
$canonical_query_string = substr($canonical_query_string, 1);

// Generate the string to be signed
$string_to_sign = "GET\nwebservices.amazon.com\n/onca/xml\n" . $canonical_query_string;

// Sign the string using HMAC-SHA256 and Base64 encoding
$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $secret_key, true));

// Add the signature to the parameters
$params["Signature"] = $signature;

// Generate the final URL
$request_url = $base_url . $canonical_query_string . "&Signature=" . rawurlencode($signature);

// Make the request
$response = file_get_contents($request_url);

// Parse the response XML
$xml = simplexml_load_string($response);

// Extract the reviewer names
$reviewers = [];
foreach ($xml->Items->Item->CustomerReviews->Review as $review) {
    $reviewers[] = (string) $review->Reviewer->Name;
}

// Print the reviewer names
print_r($reviewers);

?>
