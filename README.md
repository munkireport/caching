Caching module
===============

Get status of macOS' caching service. 

Configuration
-------------

Legacy Caching Listing

Starting with 10.13, Apple changed the caching server. In MunkiReport,
you can hide the legacy caching server listing that shows an itemized
listing for all caching server transactions for caching servers running
10.8-10.12. To hide the "Caching (Legacy)" listing, set this to FALSE.
```
CACHING_SHOW_ITEMIZED=FALSE
```
 
Table Schema
-----

* id - Unique id
* serial_number - Serial Number
* entryIndex - Entry number of caching index
* collectionDate - Date when data was written
* expirationDate - Date when data will expire
* collectionDateepoch - Date when data was written UNIX time
* requestsfrompeers - Number of requests from peers
* requestsfromclients - Number of requests from clients
* bytespurgedyoungerthan1day - Number of bytes that were less than 24 hours old that have been deleted
* bytespurgedyoungerthan7days - Number of bytes that were less than 7 days old that have been deleted
* bytespurgedyoungerthan30days - Number of bytes that were less than 30 days old that have been deleted
* bytespurgedtotal - Total number of bytes that have been deleted from cache
* bytesfrompeerstoclients - Bytes sent to clients from peers
* bytesfromorigintopeers - Bytes from origin (Apple) to peers
* bytesfromorigintoclients - Bytes from origin to clients
* bytesfromcachetopeers - Bytes from cache to peers
* bytesfromcachetoclients - Bytes from cache to clients
* bytesdropped - Number of bytes dropped
* repliesfrompeerstoclients - Number of peers replied to clients
* repliesfromorigintopeers - Number of replies from origin to peers
* repliesfromorigintoclients - Number of replies from origin to clients
* repliesfromcachetopeers - Number of replies from cache to peers
* repliesfromcachetoclients - Number of replies from cache to clients
* bytesimportedbyxpc - Bytes imported by XPC
* bytesimportedbyhttp - Bytes imported over HTTPS
* importsbyxpc - Number of XPC imports
* importsbyhttp - Number of HTTPS imports

###The above columns are used by 10.8-10.12 and 10.13.4+ clients.

###The following columns are used by 10.13+ clients.

* activated - Is caching server activated
* active - Is caching server active
* cachestatus - Status of caching server
* appletvsoftware - Bytes of Apple TV software stored in cache
* macsoftware - Bytes of Apple TV software stored in cache
* iclouddata - Bytes of Mac software stored in cache
* iossoftware - Bytes of iOS software stored in cache
* booksdata - Bytes of books stored in cache
* itunesudata - Bytes of iTunes U stored in cache
* moviesdata - Bytes of movies stored in cache
* musicdata - Bytes of music stored in cache
* otherdata - Bytes of other data stored in cache
* cachefree - Bytes of free cache
* cachelimit - Bytes of maximum cache size
* cacheused - Bytes of used cache
* personalcachefree - Bytes of personal free cache
* personalcachelimit - Bytes of maximum personal cache size
* personalcacheused - Bytes of personal used cache
* port - Port used by caching server
* publicaddress - Public address of caching server
* privateaddresses - Private address(es) of caching server
* registrationstatus - Registration status
* registrationstatus - Registration error code
* registrationresponsecode - Registration response code
* restrictedmedia - Cache contains restricted media
* serverguid - GUID of cache server
* startupstatus - Status of caching server
* totalbytesdropped - Total number of dropped bytes
* totalbytesimported - Total number of imported bytes
* totalbytesreturnedtochildren - Total number of bytes returned to children
* totalbytesreturnedtoclients - Total number of bytes returned to clients
* totalbytesreturnedtopeers - Total number of bytes returned to peers
* totalbytesstoredfromorigin - Total number of bytes stored from origin
* totalbytesstoredfromparents - Total number of bytes stored from parents
* totalbytesstoredfrompeers - Total number of bytes stored from peers
* reachability - IP address and port of caching servers that clients can reach


