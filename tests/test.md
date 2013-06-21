Running Tests in the CrawlDispatcher Project
========================

**The CrawlDispatcher using the phpunit framework for running test**

There is a simple shell wrapper script that used use to execute the test suite.

    ./bin/testrunner

To run the full suite run:

    ./bin/testrunner tests/

To run an individual test add the test file to the path, ie:

    ./bin/testrunner tests/api/JobQueueControllerTest.php

To run a specific test in a test class use the <code>--filter=testMethod</code> argment

    ./bin/testrunner --filter=testEmptyQueueRequest tests/api/JobQueueControllerTest.php


**Running end to end Gearman Monitor tests**

By default we mock the gearman interaction in the MonitorTest suite. To enable end to end testing with gearmand, add the ENV variable WITH_GEARMAND=1:

    WITH_GEARMAND=1 ./bin/testrunner tests/queue/MonitorTest.php

**note:** If the gearmand server is not running, these test will fail.

    

