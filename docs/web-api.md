Web/Rest API Endpoints for Dispatch
=============

Job Queue
-------------

**URI:** /monitor.php

**Params:** 
    <code>
    auth\_token=[token]
    jobs=[jobs json array]</code>

**Example** <code>/monitor.php?auth\_token=[token]&jobs=[jobs json array]</code>

**Returns:**
<code>
Valid   - 200 Status: {"success": "Job(s) Queued"}
Invalid - 400 Status: {"message": "[status message]"}
Invalid - 401 Status: {"error": "Invalid Auth"}
</code>



Nagios/Monitor
---------------

**URI:** /monitor.php

**Params:**
    <code>
    auth\_token=[token]
    nagios\_check=1</code>

**Example** <code>/monitor.php?auth\_token=[token]&nagios\_check=1</code>

**Returns:**
<code>
Invalid - 200 Status:   {"status":"1 running worker(s) not found - NOT OK"}
Valid   - 200 Status:   {"status":"2 running worker(s) - OK"}
Invalid - 401 Status:   {"error": "Invalid Auth"}</code>