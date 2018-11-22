<?php

return [
  /*
|===============================================
| Legacy Caching Listing
|===============================================
|
| Starting with 10.13, Apple changed the caching server. In MunkiReport,
| you can hide the legacy caching server listing that shows an itemized
| listing for all caching server transactions for caching servers running
| 10.8-10.12. To hide the "Caching (Legacy)" listing, set this to FALSE.
|
*/
  'caching_show_itemized' => env('CACHING_SHOW_ITEMIZED', true),

];
