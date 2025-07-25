<?php
class Caching_model extends \Model
{
    public function __construct($serial = '')
    {
        parent::__construct('id', 'caching'); //primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['collectiondate'] = ""; // Date when data was written
        $this->rs['expirationdate'] = ""; // Date when data will expire
        $this->rs['collectiondateepoch'] = 0; // Date when data was written, in UNIX time // Here down also used by 10.13.4+
        $this->rs['requestsfrompeers'] = 0;
        $this->rs['requestsfromclients'] = 0;
        $this->rs['bytespurgedyoungerthan1day'] = 0;
        $this->rs['bytespurgedyoungerthan7days'] = 0;
        $this->rs['bytespurgedyoungerthan30days'] = 0;
        $this->rs['bytespurgedtotal'] = 0;
        $this->rs['bytesfrompeerstoclients'] = 0;
        $this->rs['bytesfromorigintopeers'] = 0;
        $this->rs['bytesfromorigintoclients'] = 0;
        $this->rs['bytesfromcachetopeers'] = 0;
        $this->rs['bytesfromcachetoclients'] = 0;
        $this->rs['bytesdropped'] = 0;
        $this->rs['repliesfrompeerstoclients'] = 0;
        $this->rs['repliesfromorigintopeers'] = 0;
        $this->rs['repliesfromorigintoclients'] = 0;
        $this->rs['repliesfromcachetopeers'] = 0;
        $this->rs['repliesfromcachetoclients'] = 0;
        $this->rs['bytesimportedbyxpc'] = 0;
        $this->rs['bytesimportedbyhttp'] = 0;
        $this->rs['importsbyxpc'] = 0;
        $this->rs['importsbyhttp'] = 0; // Here up also used by 10.13.4+
        $this->rs['activated'] = 0; // Start of High Sierra columns
        $this->rs['active'] = 0;
        $this->rs['cachestatus'] = "";
        $this->rs['appletvsoftware'] = 0;
        $this->rs['macsoftware'] = 0;
        $this->rs['iclouddata'] = 0;
        $this->rs['iossoftware'] = 0;
        $this->rs['booksdata'] = 0;
        $this->rs['itunesudata'] = 0;
        $this->rs['moviesdata'] = 0;
        $this->rs['musicdata'] = 0;
        $this->rs['otherdata'] = 0;
        $this->rs['cachefree'] = 0;
        $this->rs['cachelimit'] = 0;
        $this->rs['cacheused'] = 0;
        $this->rs['personalcachefree'] = 0;
        $this->rs['personalcachelimit'] = 0;
        $this->rs['personalcacheused'] = 0;
        $this->rs['port'] = 0;
        $this->rs['publicaddress'] = "";
        $this->rs['privateaddresses'] = "";
        $this->rs['registrationstatus'] = 0;
        $this->rs['registrationerror'] = "";
        $this->rs['registrationresponsecode'] = "";
        $this->rs['restrictedmedia'] = 0;
        $this->rs['serverguid'] = "";
        $this->rs['startupstatus'] = null;
        $this->rs['totalbytesdropped'] = 0;
        $this->rs['totalbytesimported'] = 0;
        $this->rs['totalbytesreturnedtochildren'] = 0;
        $this->rs['totalbytesreturnedtoclients'] = 0;
        $this->rs['totalbytesreturnedtopeers'] = 0;
        $this->rs['totalbytesstoredfromorigin'] = 0;
        $this->rs['totalbytesstoredfromparents'] = 0;
        $this->rs['totalbytesstoredfrompeers'] = 0;
        $this->rs['reachability'] = ""; // end of High Sierra Columns
    }

    // ------------------------------------------------------------------------

    /**
     * Get reachability IP address for widget
     *
     **/
    public function get_reachable_cache_name()
    {
        $sql = "SELECT COUNT(CASE WHEN reachability <> '' AND reachability IS NOT NULL THEN 1 END) AS count, reachability 
                FROM caching
                LEFT JOIN reportdata USING (serial_number)
                ".get_machine_group_filter()."
                GROUP BY reachability
                ORDER BY count DESC";
        $out = [];
        foreach ($this->query($sql) as $obj) {
            if ("$obj->count" !== "0") {
                $obj->reachability = $obj->reachability ? $obj->reachability : 'Unknown';
                $out[] = $obj;
            }
        }

        return $out;
    }

    /**
     * Process data sent by postflight
     *
     * @param string data
     * @author tuxudo
     **/
    public function process($data)
    {
        // If data is empty, throw error
        if (! $data) {
            // Throw error if no data
            print_r("Error Processing Caching Module Request: No data found");
        } else if (substr( $data, 0, 26 ) != '[{"name":"status","result"' ) { // Else if old style text, process with old text based handler

            // Delete previous entries
            $this->deleteWhere('serial_number=?', $this->serial_number);

            $cache_array = array();
            $i=1;
            $c=21;
            $pastEight = (time()-691200);

            // Parse data
            foreach(explode("\n", $data) as $line) {
                $cache_line = explode("|", $line);

                if (! empty($line)) {
                      $cache_array[(str_replace(".", "", $cache_line[3]))] = $cache_line[4]; 
                      $i++;

                    if ( $i == 22 ) {
                        // Check if data is from the past 8 one days
                        if ($cache_line[1] > $pastEight){
                            $dt = new DateTime("@$cache_line[1]");
                            $cache_array['collectiondate'] = ($dt->format('Y-m-d H:i:s'));
                            $dt = new DateTime("@$cache_line[2]");
                            $cache_array['expirationdate'] = ($dt->format('Y-m-d H:i:s'));
                            $cache_array['collectiondateepoch'] = $cache_line[1];

                            foreach($cache_array as $cache_item => $item) {
                                $this->$cache_item = $cache_array[$cache_item];
                            }

                            // Save the entry
                            $this->id = '';
                            $this->create();
                            $i=1;
                        } else {
                            // If data is older than 31 days, skip it
                            $i=1; 
                        }
                    }
                }
            } // End foreach explode lines
        } else { // Process data with new, fancy pants JSON handler

            // Delete previous entries, bye bye data
            $this->deleteWhere('serial_number=?', $this->serial_number);

            // Process JSON into PHP object thingy
            $cachingjson = json_decode($data, true);

            // Translate caching object strings to db fields
            $translate = array(
                'Activated' => 'activated',
                'Active' => 'active',
                'CacheStatus' => 'cachestatus',
                'CacheFree' => 'cachefree',
                'CacheLimit' => 'cachelimit',
                'CacheUsed' => 'cacheused',
                'CacheDetails' => 'cachedetails',
                'PersonalCacheFree' => 'personalcachefree',
                'PersonalCacheLimit' => 'personalcachelimit',
                'PersonalCacheUsed' => 'personalcacheused',
                'Port' => 'port',
                'PublicAddress' => 'publicaddress',
                'PrivateAddresses' => 'privateaddresses',
                'reachability' => 'reachability',
                'RegistrationStatus' => 'registrationstatus',
                'RegistrationError' => 'registrationerror',
                'RegistrationResponseCode' => 'registrationresponsecode',
                'RestrictedMedia' => 'restrictedmedia',
                'ServerGUID' => 'serverguid',
                'StartupStatus' => 'startupstatus',
                'TotalBytesDropped' => 'totalbytesdropped',
                'TotalBytesImported' => 'totalbytesimported',
                'TotalBytesReturnedToChildren' => 'totalbytesreturnedtochildren',
                'TotalBytesReturnedToClients' => 'totalbytesreturnedtoclients',
                'TotalBytesReturnedToPeers' => 'totalbytesreturnedtopeers',
                'TotalBytesStoredFromOrigin' => 'totalbytesstoredfromorigin',
                'TotalBytesStoredFromParents' => 'totalbytesstoredfromparents',
                'TotalBytesStoredFromPeers' => 'totalbytesstoredfrompeers'
            );

            $cachedetailstranslate = array (
                'Apple TV Software' => 'appletvsoftware',
                'Mac Software' => 'macsoftware',
                'iCloud' => 'iclouddata',
                'iOS Software' => 'iossoftware',
                'Books' => 'booksdata',
                'iTunes U' => 'itunesudata',
                'Movies' => 'moviesdata',
                'Music' => 'musicdata',
                'Other' => 'otherdata',
            );

            $booleans = array('activated','active','registrationstatus','restrictedmedia');

            $nestedarrays = array('cachedetails');

            // Traverse the caching object with translations
            foreach ($translate as $search => $field) { 

                if ((is_null($cachingjson) || ! is_array($cachingjson) || ! array_key_exists(0, $cachingjson) || ! is_array($cachingjson[0]["result"])) && ! in_array($field, $booleans)){
                    // If not an array, null the field
                    $this->$field = '';

                } else if (! array_key_exists($search, $cachingjson[0]["result"]) && ! in_array($field, $booleans)){
                    // Skip keys that may not exist and null the value
                    $this->$field = '';

                    // Format booleans before processing
                } else if (in_array($field, $booleans) && ($cachingjson[0]["result"][$search] == "true" || $cachingjson[0]["result"][$search] == "1")) {
                    // Send a 1 to the db
                    $this->$field = '1';

                } else if (in_array($field, $booleans) && ($cachingjson[0]["result"][$search] == "false" || $cachingjson[0]["result"][$search] == "0" || $cachingjson[0]["result"][$search] == "")) {
                    // Send a 0 to the db
                    $this->$field = '0';

                } else if (! empty($cachingjson[0]["result"][$search]) && ! is_array($cachingjson[0]["result"][$search])) {
                    // If key is not empty, save it to the object
                    $this->$field = $cachingjson[0]["result"][$search];
                    
                } else if (is_array($cachingjson[0]["result"][$search]) && ! in_array($field, $nestedarrays) && ! empty($cachingjson[0]["result"][$search])){
                    // If is an array and not a nested array, and is not empty, condense it to a string and save it
                    $this->$field = implode(", ", $cachingjson[0]["result"][$search]);

                } else if ($search == "CacheDetails" && ! empty($cachingjson[0]["result"][$search])){
                    // Fill out the caching details values from the CacheDetails array

                    foreach ($cachedetailstranslate as $detailssearch => $detailsfield){
                        if (! empty($cachingjson[0]["result"][$search][$detailssearch])) {
                            // If detail search isn't empty, save value
                            $this->$detailsfield = $cachingjson[0]["result"][$search][$detailssearch];
                        } else {
                            // Else, set value to zero
                            $this->$detailsfield = "0";
                        }
                    }

                } else if ($cachingjson[0]["result"][$search] == "0" && ! is_array($cachingjson[0]["result"][$search])){
                    // Set the value to 0 if it's 0
                    $this->$field = "0";
                } else {
                    // Else, null the value
                    $this->$field = '';
                }
            }

            if(is_array($cachingjson) && array_key_exists(0, $cachingjson) && is_array($cachingjson[0]["result"]) && array_key_exists('exact_metrics', $cachingjson[0]["result"]) && $cachingjson[0]["result"]['exact_metrics']){
                $this->expirationdate = -1;
            }

            // Save it, like that cake you just dropped onto the floor after pulling it out of the oven. Yea, I saw that
            $this->id = '';
            $this->create();

            // Process exact_metrics, if it exists
            if(is_array($cachingjson) && array_key_exists(0, $cachingjson) && is_array($cachingjson[0]["result"]) && array_key_exists('exact_metrics', $cachingjson[0]["result"]) && $cachingjson[0]["result"]['exact_metrics']){
                $pastThirtyone = (time()-2678400);
                // Split entries into array
                $exact_array = explode("__", $cachingjson[0]["result"]['exact_metrics']);
                // Process each entry
                foreach($exact_array as $metric){
                    // Make sure entry isn't empty and not _
                    if (! empty($metric) && $metric !== "_") {

                        // Split entry
                        $metric_array = explode(",",$metric);

                        // Format collection date epoch
                        $metric_array[0] = (round(explode(".",$metric_array[0])[0])+978307200);

                        // Only save data if it is less than 31 days old
                        if ($metric_array[0] > $pastThirtyone){
                            // Assign data to columns from metric_array
                            $this->collectiondateepoch = $metric_array[0];
                            $this->requestsfrompeers = $metric_array[1];
                            $this->requestsfromclients = $metric_array[2];
                            $this->bytespurgedyoungerthan1day = $metric_array[3];
                            $this->bytespurgedyoungerthan7days = $metric_array[4];
                            $this->bytespurgedyoungerthan30days = $metric_array[5];
                            $this->bytespurgedtota = $metric_array[6];
                            $this->bytesfrompeerstoclients = $metric_array[7];
                            $this->bytesfromorigintopeers = $metric_array[8];
                            $this->bytesfromorigintoclients = $metric_array[9];
                            $this->bytesfromcachetopeers = $metric_array[10];
                            $this->bytesfromcachetoclients =$metric_array[11];
                            $this->bytesdropped = $metric_array[12];
                            $this->repliesfrompeerstoclients = $metric_array[13];
                            $this->repliesfromorigintopeers = $metric_array[14];
                            $this->repliesfromorigintoclients = $metric_array[15];
                            $this->repliesfromcachetopeers = $metric_array[16];
                            $this->repliesfromcachetoclients = $metric_array[17];
                            $this->bytesimportedbyxpc = $metric_array[17];
                            $this->bytesimportedbyhttp = $metric_array[17];
                            $this->importsbyxpc = $metric_array[18];
                            $this->importsbyhttp = $metric_array[19];

                            // null out the rest of the columns because we don't need duplicate data
                            $this->activated = null;
                            $this->active = null;
                            $this->cachestatus = null;
                            $this->appletvsoftware = null;
                            $this->macsoftware = null;
                            $this->iclouddata = null;
                            $this->iossoftware = null;
                            $this->booksdata = null;
                            $this->itunesudata = null;
                            $this->moviesdata = null;
                            $this->musicdata = null;
                            $this->otherdata = null;
                            $this->cachefree = null;
                            $this->cachelimit = null;
                            $this->cacheused = null;
                            $this->personalcachefree = null;
                            $this->personalcachelimit = null;
                            $this->personalcacheused = null;
                            $this->port = null;
                            $this->publicaddress = null;
                            $this->privateaddresses = null;
                            $this->registrationstatus = null;
                            $this->registrationerror = null;
                            $this->registrationresponsecode = null;
                            $this->restrictedmedia = null;
                            $this->serverguid = null;
                            $this->startupstatus = null;
                            $this->totalbytesdropped = null;
                            $this->totalbytesimported = null;
                            $this->totalbytesreturnedtochildren = null;
                            $this->totalbytesreturnedtoclients = null;
                            $this->totalbytesreturnedtopeers = null;
                            $this->totalbytesstoredfromorigin = null;
                            $this->totalbytesstoredfromparents = null;
                            $this->totalbytesstoredfrompeers = null;
                            $this->reachability = null;

                            // Format the date
                            $dt = new DateTime("@$metric_array[0]");
                            $this->collectiondate = ($dt->format('Y-m-d H:i:s'));
                            $expireationdate = $metric_array[0]+604800;
                            $dt = new DateTime("@$expireationdate");
                            $this->expirationdate = ($dt->format('Y-m-d H:i:s'));

                            // Save the metric, because we want that data. Mmmmm data, tastes like floor cake
                            $this->id = '';
                            $this->create();
                        }
                    }
                }
            }
        }
    } // End process()
}
