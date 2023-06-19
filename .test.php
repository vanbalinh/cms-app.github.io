<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = getClient();

$service = new Google_Service_Sheets($client);

// The ID of the spreadsheet to update.
$spreadsheetId = 'my-spreadsheet-id'; // TODO: Update placeholder value.

// TODO: Assign values to desired properties of `requestBody`:
$requestBody = new Google_Service_Sheets_BatchUpdateValuesRequest();

$response = $service->spreadsheets_values->batchUpdate($spreadsheetId, $requestBody);

// TODO: Change code below to process the `response` object:
echo '<pre>', var_export($response, true), '</pre>', "\n";

function getClient()
{
    // TODO: Change placeholder below to generate authentication credentials. See
    // https://developers.google.com/sheets/quickstart/php#step_3_set_up_the_sample
    //
    // Authorize using one of the following scopes:
    //   'https://www.googleapis.com/auth/drive'
    //   'https://www.googleapis.com/auth/drive.file'
    //   'https://www.googleapis.com/auth/spreadsheets'
    return "371205064724-nakqskace828vuk31hc9ebo2ospk2ir0.apps.googleusercontent.com";
}
